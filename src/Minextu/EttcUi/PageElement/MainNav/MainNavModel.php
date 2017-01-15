<?php namespace Minextu\EttcUi\PageElement\MainNav;

use Minextu\EttcUi\PageElement\AbstractPageElementModel;

class MainNavModel extends AbstractPageElementModel
{
    /**
     * Returns all Entries that should be in the main navigation
     * @return   array   all entries in the main navigation. Keys contain the text, values contain the url
     */
    public function getEntries()
    {
        $entries = array(
            "Projects" => "Projects",
            "Server" => "Server"
        );

        return $entries;
    }
}
