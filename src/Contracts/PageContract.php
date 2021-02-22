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
}