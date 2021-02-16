<?php


namespace HansSchouten\LaravelPageBuilder;


use PHPageBuilder\Contracts\ThemeContract;
use PHPageBuilder\Modules\GrapesJS\Block\BaseController;
use PHPageBuilder\ThemeBlock;

class ThemeBlockWrapper extends ThemeBlock
{
    protected $forPageBuilder;
    protected $modelFile = "model.php";
    protected $builderViewFile = "builder_view.blade.php";
    protected $viewFile = "view.blade.php";
    protected $htmlViewFile = "view.html";
    protected $controllerFile = "controller.php";
    protected $controllerClassName = "Controller";
    public function __construct(ThemeContract $theme, string $blockSlug, $forPageBuilder)
    {
        $this->forPageBuilder = $forPageBuilder;
        parent::__construct($theme, $blockSlug);
    }

    public function isPhpBlock()
    {
        $basePath = $this->getFolder() . '/';
        if($this->forPageBuilder){
            return file_exists($basePath . $this->builderViewFile);
        }
        else {
            return file_exists($basePath . $this->viewFile);
        }
    }

    public function getViewFile()
    {
        $basPath =  $this->getFolder() . '/';
        if ($this->isPhpBlock()) {
            if ($this->forPageBuilder) {
                return $basPath . $this->builderViewFile;
            }
            else {
                return $basPath . $this->viewFile;
            }
        }
        return $basPath . $this->htmlViewFile;
    }

    public function getModelFile()
    {
        $file = $this->getFolder() . '/' . $this->modelFile;
        if(file_exists($file)){
            return $file;
        }
        return null;
    }

    public function getControllerFile()
    {
        $path = $this->getFolder() . '/' . $this->controllerFile;
        if (file_exists($path)) {
            return $path;
        }
        return null;
    }
    /**
     * Return the controller class of this theme block.
     *
     * @return string
     */
    public function getControllerClass()
    {
        if (file_exists($this->getFolder() . '/' . $this->controllerFile)) {
            return $this->getNamespace() . '\\' . $this->controllerClassName;
        }
        return BaseController::class;
    }

}