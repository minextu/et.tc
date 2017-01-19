<?php namespace Minextu\EttcUi\Page;

use Minextu\EttcUi\AbstractView;

abstract class AbstractPageView extends AbstractView
{
    /**
     * Get the title for this page
     * @return   string   Title of this page
     */
    abstract public function getTitle();
    /**
     * Get the heading for this page
     * @return   string   Heading for this page
     */
    abstract public function getHeading();

    /**
     * Get the optional sub heading
     * @return   string|bool   False or the sub heading for this page
     */
    public function getSubHeading()
    {
        return false;
    }
}
