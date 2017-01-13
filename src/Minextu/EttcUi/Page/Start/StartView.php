<?php namespace Minextu\EttcUi\Page\Start;
use Minextu\EttcUi\Page\AbstractPageView;

class StartView extends AbstractPageView
{
    function getTitle()
    {
        return "Start";
    }

    function getHeading()
    {
        return "Start";
    }

    function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/StartView.html");
    }
}
