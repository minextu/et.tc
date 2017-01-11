<?php namespace nexttrex\EttcUi\Page;
use nexttrex\EttcUi\AbstractView;

abstract class AbstractPageView extends AbstractView
{
    /**
     * Get the title for this page
     * @return   string   Title of this page
     */
    abstract function getTitle();
    /**
     * Get the heading for this page
     * @return   string   Heading for this page
     */
    abstract function getHeading();

    /**
     * Get the optional sub heading
     * @return   string|bool   False or the sub heading for this page
     */
    function getSubHeading()
    {
        return false;
    }
}
