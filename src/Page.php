<?php
namespace HansSchouten\LaravelPageBuilder;
use HansSchouten\LaravelPageBuilder\Contracts\PageContract;
use PHPageBuilder\Page as BasePage;

class Page extends BasePage implements PageContract
{
    /**
     * @var array variables that should be accessible in the view
     */
    protected $variables;

    /**
     * Gets the page meta
     *
     * @return array|null
     */
    public function getMeta(){
        if(isset($this->attributes['meta'])){
            return json_decode($this->attributes['meta'], true);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

}