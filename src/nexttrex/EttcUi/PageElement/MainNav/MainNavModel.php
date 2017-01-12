<?php namespace nexttrex\EttcUi\PageElement\MainNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementModel;

class MainNavModel extends AbstractPageElementModel
{
    function getEntries()
    {
        $entries = array(
            "Projects" => "?page=Projects",
            "Server" => "?page=Server"
        );

        return $entries;
    }
}
