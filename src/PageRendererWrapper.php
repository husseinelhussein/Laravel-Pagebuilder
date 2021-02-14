<?php
namespace HansSchouten\LaravelPageBuilder;

use PHPageBuilder\Modules\GrapesJS\PageRenderer;

class PageRendererWrapper extends PageRenderer {

    /** @var ThemeWrapper */
    protected $theme;
    public function getPageLayoutPath()
    {
        $layout = basename($this->page->getLayout());
        $slug = $this->theme->getThemeSlug();
        $path = 'pagebuilder::' . $slug . '.layouts.' . $layout;
        if($this->forPageBuilder){
            $path .=  '.builder_view';
        }
        else {
            $path .= '.view';
        }
        return $path;
    }

    public function render()
    {
        if ($this->forPageBuilder) {
            $body = '<div phpb-content-container="true"></div>';
        } else {
            $body = $this->renderBody();
        }

        $layoutPath = $this->getPageLayoutPath();
        if ($layoutPath) {
            // init variables that should be accessible in the view
            $vars = [
                'renderer' => $this,
                'page' => $this->page,
                'body' => $body,
                'forPageBuilder' => $this->forPageBuilder,
            ];
            $pageHtml = view()->make($layoutPath, $vars)->render();
        } else {
            $pageHtml = $body;
        }

        // parse any shortcodes present in the page layout
        $pageHtml = $this->parseShortcodes($pageHtml);

        return $pageHtml;
    }

    /** overrides the function to use BlockRendererWrapper and ThemeBlockWrapper.
     *
     * @param $slug
     * @param null $id
     * @param null $context
     * @param int $maxDepth
     * @return mixed|string
     * @throws \Exception
     */
    public function renderBlock($slug, $id = null, $context = null, $maxDepth = 25)
    {
        $themeBlock = new ThemeBlockWrapper($this->theme, $slug);
        $id = $id ?? $themeBlock->getSlug();
        $context = $context[$id] ?? $this->pageBlocksData[$id] ?? [];

        $blockRenderer = new BlockRendererWrapper($this->theme, $this->page, $this->forPageBuilder);
        $renderedBlock = $blockRenderer->render($themeBlock, $context ?? [], $id);

        // determine the context for rendering nested blocks
        // if the current block is an html block, the context starts again at full page data
        // if the current block is a dynamic block, use the nested block data inside the current block's context
        $context = $context['blocks'] ?? [];
        if ($themeBlock->isHtmlBlock()) {
            $context = $this->pageBlocksData;
        }

        return $this->shortcodeParser->doShortcodes($renderedBlock, $context, $maxDepth - 1);
    }
}