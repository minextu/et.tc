<?php namespace nexttrex\EttcUi\Page;
use nexttrex\EttcUi\ModelInterface;

abstract class AbstractPageModel implements ModelInterface
{
    abstract function getTitle();
    abstract function getHeading();

    function getSubHeading()
    {
        return false;
    }
}
