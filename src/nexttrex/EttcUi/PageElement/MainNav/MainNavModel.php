<?php namespace nexttrex\EttcUi\PageElement\MainNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementModel;

class MainNavModel extends AbstractPageElementModel
{
    function getEntries()
    {
        $entries = array(
            "Projects" => "Projects",
            "Server" => "Server"
        );

        return $entries;
    }
}
