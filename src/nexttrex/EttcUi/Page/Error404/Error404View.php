<?php namespace nexttrex\EttcUi\Page\Error404;
use nexttrex\EttcUi\Page\AbstractPageView;

class Error404View extends AbstractPageView
{
    function getTitle()
    {
        return "Page not found";
    }

    function getHeading()
    {
        return "Error 404";
    }

    function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/Error404View.html");
    }
}
