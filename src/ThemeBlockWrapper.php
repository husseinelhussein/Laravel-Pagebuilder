<?php


namespace HansSchouten\LaravelPageBuilder;


use PHPageBuilder\Contracts\ThemeContract;
use PHPageBuilder\ThemeBlock;

class ThemeBlockWrapper extends ThemeBlock
{
    protected $forPageBuilder;
    public function __construct(ThemeContract $theme, string $blockSlug, $forPageBuilder)
    {
        $this->forPageBuilder = $forPageBuilder;
        parent::__construct($theme, $blockSlug);
    }

    public function isPhpBlock()
    {
        if($this->forPageBuilder){
            return file_exists($this->getFolder() . '/view_builder.blade.php');
        }
        else {
            return file_exists($this->getFolder() . '/view.blade.php');
        }
    }

    public function getViewFile()
    {
        if ($this->isPhpBlock()) {
            if ($this->forPageBuilder) {
                return $this->getFolder() . '/builder_view.blade.php';
            }
            else {
                return $this->getFolder() . '/view.blade.php';
            }
        }
        return $this->getFolder() . '/view.html';
    }
}