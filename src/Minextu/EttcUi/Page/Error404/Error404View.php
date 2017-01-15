<?php namespace Minextu\EttcUi\Page\Error404;

use Minextu\EttcUi\Page\AbstractPageView;

class Error404View extends AbstractPageView
{
    public function getTitle()
    {
        return "Page not found";
    }

    public function getHeading()
    {
        return "Error 404";
    }

    public function getSubHeading()
    {
        return "Page not found";
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/Error404View.html");
    }
}
