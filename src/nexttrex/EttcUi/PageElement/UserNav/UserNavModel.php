<?php namespace nexttrex\EttcUi\PageElement\UserNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementModel;
use nexttrex\Ettc\Account\Account;

class UserNavModel extends AbstractPageElementModel
{
    private $user;

    function init()
    {
        $this->user = Account::checkLogin($this->mainModel->getDb());
    }

    function checkLogin()
    {
        if ($this->user)
            return true;
        else
            return false;
    }

    function getNickname()
    {
        if ($this->user)
            return $this->user->getNick();
        else
            return false;
    }

    function getAvatar()
    {
        $default = "http://img2.wikia.nocookie.net/__cb20110302033947/recipes/images/thumb/1/1c/Avatar.svg/480px-Avatar.svg.png";
        $size = 200;

        if ($this->user)
            $avatar = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->user->getEmail() ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size . "&r=g";
        else
            $avatar = $default;

        return $avatar;
    }
}
