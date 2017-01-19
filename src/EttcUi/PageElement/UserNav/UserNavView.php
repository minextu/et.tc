<?php namespace Minextu\EttcUi\PageElement\UserNav;

use Minextu\EttcUi\PageElement\AbstractPageElementView;

class UserNavView extends AbstractPageElementView
{
    private $loggedIn = false;

    public function generateHtml()
    {
        $placeholders = [
            'MSG_Nickname' => $this->presenter->getNickname(),
            'IMG_Avatar' => $this->presenter->getAvatar()
        ];

        if ($this->loggedIn) {
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedIn.html", $placeholders);
        } else {
            $html = $this->template->convertTemplate(__DIR__."/templates/UserNavLoggedOut.html", $placeholders);
        }

        return $html;
    }

    /**
     * @param   bool   $loggedIn   User login status
     */
    public function setLoggedIn($loggedIn)
    {
        $this->loggedIn = $loggedIn;
    }
}
