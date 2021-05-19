<?php


namespace HansSchouten\LaravelPageBuilder;


use HansSchouten\LaravelPageBuilder\Contracts\ThemeContract;
use PHPageBuilder\Modules\GrapesJS\Block\BaseController;
use PHPageBuilder\ThemeBlock;

class ThemeBlockWrapper extends ThemeBlock
{
    protected $forPageBuilder;
    protected $modelFile = "model.php";
    protected $builderViewFile = "builder_view.blade.php";
    protected $viewFile = "view.blade.php";
    protected $phpViewFile = "view.php";
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
            $exists = file_exists($basePath . $this->builderViewFile);
            if(!$exists){
                $exists = file_exists($basePath . $this->viewFile);
            }
            return $exists;
        }
        else {
            return file_exists($basePath . $this->viewFile);
        }
    }

    public function getViewFile()
    {
        $basePath =  $this->getFolder() . '/';
        if ($this->isPhpBlock()) {
            if ($this->forPageBuilder) {
                if(file_exists($basePath . $this->builderViewFile)){
                    return $basePath . $this->builderViewFile;
                }
                else if(file_exists($basePath . $this->viewFile)){
                    return $basePath . $this->viewFile;
                }
                else if(file_exists($basePath . $this->phpViewFile)){
                    return $basePath . $this->phpViewFile;
                }
            }
            elseif(file_exists($basePath . $this->viewFile)){
                return $basePath . $this->viewFile;
            }
            elseif(file_exists($basePath . $this->viewFile)){
                return $basePath . $this->viewFile;
            }
        }
        return $basePath . $this->htmlViewFile;
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

    /**
     * Return the file path of the thumbnail of this block.
     *
     * @return string
     */
    public function getThumbPath()
    {
        return $this->get('thumbnail');
    }

    public function getThumbUrl()
    {
        $base = phpb_config('general.assets_url') . '/' . $this->theme->getThemeSlug();
        $asset = asset($base . '/assets/block-thumbs/' . $this->get('thumbnail'));
        return $asset;
    }

}