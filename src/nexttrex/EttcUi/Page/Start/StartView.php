<?php namespace nexttrex\EttcUi\Page\Start;
use nexttrex\EttcUi\Page\AbstractPageView;

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
