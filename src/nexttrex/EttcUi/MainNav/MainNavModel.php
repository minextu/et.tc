<?php namespace nexttrex\ettcUi\mainNav;
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
