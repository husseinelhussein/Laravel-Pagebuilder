<?php


namespace HansSchouten\LaravelPageBuilder;


use PHPageBuilder\Theme;
use PHPageBuilder\ThemeBlock;
use DirectoryIterator;
class ThemeWrapper extends Theme
{
    /**
     * Overrides the function to use ThemeBlockWrapper.
     */
    protected function loadThemeBlocks()
    {
        $this->blocks = [];

        if (! file_exists($this->getFolder() . '/blocks')) {
            return;
        }
        $forPageBuilder = isset($_GET['page']) && !empty($_GET['page']);
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
     * @return string
     */
    public function getThemeSlug(): string
    {
        return $this->themeSlug;
    }
}