<?php
namespace HansSchouten\LaravelPageBuilder\Contracts;

use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Contracts\ThemeContract as BaseThemeContract;
use stringEncode\Exception;

interface ThemeContract extends BaseThemeContract
{
    /**
     * Gets the back button link for editor.
     *
     * @param PageContract $page the current page being edited.
     * @param null $locale
     *
     * @return string
     */
    public function getBackLink(PageContract $page, $locale = null): string;

    /**
     * Gets the back text for editor.
     *
     * @param null $locale
     *
     * @return string
     */
    public function getBackText($locale = null): string;

    /**
     * Gets the view button link for editor.
     *
     * @param PageContract $page the current page being edited.
     * @param null $locale
     *
     * @return string
     */

    public function getViewLink(PageContract $page, $locale = null): string;

    /**
     * Gets the view button text for editor.
     *
     * @param null $locale
     *
     * @return string
     */
    public function getViewText($locale = null): string;

    /**
     * Gets the theme slug.
     *
     * @return string
     */
    public function getThemeSlug(): string;
}