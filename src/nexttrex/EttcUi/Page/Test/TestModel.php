<?php namespace nexttrex\EttcUi\Page\Test;
use \nexttrex\EttcUi\Page\AbstractPageModel;

class TestModel extends AbstractPageModel
{
    function getTitle()
    {
        return "Test Title";
    }

    function getHeading()
    {
        return "Test Heading";
    }
}
