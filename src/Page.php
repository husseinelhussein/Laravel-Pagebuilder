<?php
namespace HansSchouten\LaravelPageBuilder;
use PHPageBuilder\Page as BasePage;

class Page extends BasePage
{
    public function getRoute($locale = null)
    {
        // route > slug
        $routeTranslation = $this->getTranslation('route', $locale);
        return route('shop.cms.page', ['slug' => $routeTranslation]);
    }
}