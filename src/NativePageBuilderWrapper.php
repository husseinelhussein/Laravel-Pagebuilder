<?php

namespace HansSchouten\LaravelPageBuilder;

use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Modules\GrapesJS\Block\BlockAdapter;
use PHPageBuilder\Modules\GrapesJS\PageBuilder;
use PHPageBuilder\Modules\GrapesJS\PageRenderer;
use PHPageBuilder\Repositories\UploadRepository;

class NativePageBuilderWrapper extends PageBuilder
{

    public function renderPageBuilder(PageContract $page)
    {
        phpb_set_in_editmode();

        // init variables that should be accessible in the view
        $pageBuilder = $this;
        $pageRenderer = phpb_instance(PageRenderer::class, [$this->theme, $page, true]);

        // create an array of theme blocks and theme block settings for in the page builder sidebar
        $blocks = [];
        $blockSettings = [];
        foreach ($this->theme->getThemeBlocks() as $themeBlock) {
            $slug = phpb_e($themeBlock->getSlug());
            $adapter = new BlockAdapter($pageRenderer, $themeBlock);
            $blockSettings[$slug] = $adapter->getBlockSettingsArray();

            if ($themeBlock->get('hidden') !== true) {
                $blocks[$slug] = $adapter->getBlockManagerArray();
            }
        }

        // create an array of all uploaded assets
        $assets = [];
        foreach ((new UploadRepository)->getAll() as $file) {
            $assets[] = [
                'src' => $file->getUrl(),
                'public_id' => $file->public_id
            ];
        }

        require __DIR__ . '/resources/views/layout.php';
    }
}
