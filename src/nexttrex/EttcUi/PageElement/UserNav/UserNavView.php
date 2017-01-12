<?php namespace nexttrex\EttcUi\PageElement\UserNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementView;

class UserNavView extends AbstractPageElementView
{
    private $entries;
    private $loggedIn = false;

    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    function generateHtml()
    {
        if ($this->loggedIn)
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedIn.html");
        else
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedOut.html");

        return $html;
    }

    function setLoggedIn($loggedIn)
    {
        $this->loggedIn = $loggedIn;
    }
}
