<?php namespace nexttrex\EttcUi\MainNav;
use nexttrex\ettcUi\ModelInterface;

class MainNavModel implements ModelInterface
{
    function getEntries()
    {
        $entries = array(
            "Start" => "?page=start",
            "Projects" => "?page=projects"
        );

        return $entries;
    }
}
