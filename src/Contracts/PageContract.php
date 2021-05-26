<?php


namespace HansSchouten\LaravelPageBuilder\Contracts;

use PHPageBuilder\Contracts\PageContract as BaseContract;

interface PageContract extends BaseContract
{
    /**
     * Gets the page meta
     *
     * @return array|null
     */
    public function getMeta();

    /**
     * @return array
     */
    public function getVariables(): array;

    /**
     * @param array $variables
     */
    public function setVariables(array $variables):void;

    /**
     * Magic method to get a Page attribute.
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get(string $attribute);

    /**
     * @param string $setting
     * @param null $locale
     * @return PageTranslationContract|string|null
     */
    public function getTranslation(string $setting, $locale = null);
}