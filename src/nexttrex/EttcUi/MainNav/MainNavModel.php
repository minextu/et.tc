<?php namespace nexttrex\EttcUi\MainNav;
use nexttrex\ettcUi\ModelInterface;

class MainNavModel implements ModelInterface
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
