<?php

namespace HansSchouten\LaravelPageBuilder;

use Illuminate\View\View;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Modules\GrapesJS\PageBuilder;
use PHPageBuilder\Modules\GrapesJS\PageRenderer;
use PHPageBuilder\Modules\GrapesJS\Thumb\ThumbGenerator;
use PHPageBuilder\Repositories\PageRepository;
use PHPageBuilder\Repositories\UploadRepository;

class NativePageBuilderWrapper extends PageBuilder
{

    /**
     * @param $route
     * @param $action
     * @param PageContract|null $page
     * @return bool|View
     * @throws \Exception
     */
    public function handleRequest($route, $action, PageContract $page = null)
    {
        phpb_set_in_editmode();

        if ($route === 'thumb_generator') {
            $thumbGenerator = new ThumbGenerator($this->theme);
            return $thumbGenerator->handleThumbRequest($action);
        }

        if (is_null($page)) {
            $pageId = $_GET['page'] ?? null;
            $pageRepository = new PageRepository;
            $page = $pageRepository->findWithId($pageId);
        }
        if (! ($page instanceof PageContract)) {
            return false;
        }

        switch ($action) {
            case null:
            case 'edit':
                return $this->renderPageBuilder($page);
            case 'store':
                if (isset($_POST) && isset($_POST['data'])) {
                    $data = json_decode($_POST['data'], true);
                    $this->updatePage($page, $data);
                    exit();
                }
                break;
            case 'upload':
                if (isset($_FILES)) {
                    $this->handleFileUpload();
                }
                break;
            case 'upload_delete':
                if (isset($_POST['id'])) {
                    $this->handleFileDelete();
                }
                break;
            case 'renderBlock':
                if (isset($_POST['language']) && isset($_POST['data']) && isset(phpb_active_languages()[$_POST['language']])) {
                    $this->renderPageBuilderBlock($page, $_POST['language'], json_decode($_POST['data'], true));
                    exit();
                }
                break;
            case 'renderLanguageVariant':
                if (isset($_POST['language']) && isset($_POST['data']) && isset(phpb_active_languages()[$_POST['language']])) {
                    $this->renderLanguageVariant($page, $_POST['language'], json_decode($_POST['data'], true));
                    exit();
                }
                break;
        }

        return false;
    }

    /**
     * Overrides the original method to return a blade view instead.
     * @param PageContract $page
     * @throws \Exception
     */
    public function renderPageBuilder(PageContract $page)
    {
        phpb_set_in_editmode();

        // init variables that should be accessible in the view
        $pageBuilder = $this;
        $pageRenderer = phpb_instance(PageRendererWrapper::class, [$this->theme, $page, true]);

        // create an array of theme blocks and theme block settings for in the page builder sidebar
        $blocks = [];
        $blockSettings = [];
        foreach ($this->theme->getThemeBlocks() as $themeBlock) {
            $slug = phpb_e($themeBlock->getSlug());
            $adapter = new BlockAdapterWrapper($pageRenderer, $themeBlock);
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
        $vars = [
          'pageBuilder' => $this,
          'page' => $page,
          'blocks' => $blocks,
          'blockSettings' => $blockSettings,
          'pageRenderer' => $pageRenderer,
          'assets' => $assets,
        ];
        return view('pagebuilder::layout', $vars);
    }

    /**
     * Overrides the function to return PageRendererWrapper
     * @param PageContract $page
     * @param null $language
     * @return string
     */
    public function renderPage(PageContract $page, $language = null): string
    {
        $pageRenderer = phpb_instance(PageRendererWrapper::class, [$this->theme, $page]);
        if (! is_null($language)) {
            $pageRenderer->setLanguage($language);
        }
        return $pageRenderer->render();
    }

}
