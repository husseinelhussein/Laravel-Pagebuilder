<?php


namespace HansSchouten\LaravelPageBuilder\Contracts;

use PHPageBuilder\Contracts\PageTranslationContract as BaseContract;
interface PageTranslationContract extends BaseContract
{

    /**
     * Magic method to get a PageTranslation attribute.
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute);

}