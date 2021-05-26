<?php

namespace HansSchouten\LaravelPageBuilder;

use HansSchouten\LaravelPageBuilder\Contracts\PageTranslationContract;
use HansSchouten\LaravelPageBuilder\Repositories\PageRepository;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\PageTranslation as BaseTranslation;

class PageTranslation extends BaseTranslation implements PageTranslationContract
{
    /**
     * Return the page this translation belongs to.
     *
     * @return PageContract
     */
    public function getPage()
    {
        $foreignKey = phpb_config('page.translation.foreign_key');
        return (new PageRepository())->findWithId($this->{$foreignKey});
    }

    /**
     * @inheritDoc
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->get($attribute);
        }
        return null;
    }
}
