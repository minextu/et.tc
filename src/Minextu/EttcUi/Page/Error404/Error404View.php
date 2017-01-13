<?php namespace Minextu\EttcUi\Page\Error404;
use Minextu\EttcUi\Page\AbstractPageView;

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

    function getSubHeading()
    {
        return "Page not found";
    }

    function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/Error404View.html");
    }
}
