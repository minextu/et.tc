<?php namespace nexttrex\EttcUi\Page\Test;
use nexttrex\EttcUi\Page\AbstractPageView;

class TestView extends AbstractPageView
{
    function getTitle()
    {
        return "Test";
    }

    function getHeading()
    {
        return "Test";
    }

    function generateHtml()
    {
        return "Test";
    }
}
