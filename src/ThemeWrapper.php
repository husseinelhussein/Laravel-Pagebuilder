<?php


namespace HansSchouten\LaravelPageBuilder;


use HansSchouten\LaravelPageBuilder\Contracts\ThemeContract;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Theme;
use DirectoryIterator;
class ThemeWrapper extends Theme implements ThemeContract
{
    /**
     * @inheritDoc
     */
    public function getBackLink(PageContract $page, $locale = null): string
    {
        return phpb_e(phpb_full_url(phpb_config('pagebuilder.actions.back')));
    }

    /**
     * @inheritDoc
     */
    public function getBackText($locale = null): string
    {
        return phpb_trans('pagebuilder.go-back');
    }

    /**
     * @inheritDoc
     */
    public function getViewLink(PageContract $page, $locale = null): string
    {
        return phpb_e(phpb_full_url($page->getRoute($locale)));
    }

    /**
     * @inheritDoc
     */
    public function getViewText($locale = null): string
    {
        return phpb_trans('pagebuilder.view-page');
    }

    /**
     * Overrides the function to use ThemeBlockWrapper.
     */
    protected function loadThemeBlocks()
    {
        $this->blocks = [];

        if (! file_exists($this->getFolder() . '/blocks')) {
            return;
        }
        $forPageBuilder = isset($_GET['page_id']) && !empty($_GET['page_id']);
        $blocksDirectory = new DirectoryIterator($this->getFolder() . '/blocks');
        foreach ($blocksDirectory as $entry) {
            if ($entry->isDir() && ! $entry->isDot()) {
                $blockSlug = $entry->getFilename();
                $block = new ThemeBlockWrapper($this, $blockSlug, $forPageBuilder);
                $this->blocks[$blockSlug] = $block;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getThemeSlug(): string
    {
        return $this->themeSlug;
    }
}