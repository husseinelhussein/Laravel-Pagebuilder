<?php


namespace HansSchouten\LaravelPageBuilder;


use PHPageBuilder\ThemeBlock;

class ThemeBlockWrapper extends ThemeBlock
{
    public function isPhpBlock()
    {
        return file_exists($this->getFolder() . '/view.blade.php');
    }

    public function getViewFile()
    {
        if ($this->isPhpBlock()) {
            return $this->getFolder() . '/view.blade.php';
        }
        return $this->getFolder() . '/view.html';
    }
}