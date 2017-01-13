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
        $placeholders = [
            'MSG_Nickname' => $this->presenter->getNickname(),
            'IMG_Avatar' => $this->presenter->getAvatar()
        ];

        if ($this->loggedIn)
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedIn.html", $placeholders);
        else
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedOut.html", $placeholders);

        return $html;
    }

    function setLoggedIn($loggedIn)
    {
        $this->loggedIn = $loggedIn;
    }
}
