<?php
namespace HansSchouten\LaravelPageBuilder;
use HansSchouten\LaravelPageBuilder\Contracts\PageContract;
use PHPageBuilder\Page as BasePage;

class Page extends BasePage implements PageContract
{
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
}