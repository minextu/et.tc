<?php namespace Minextu\EttcUi\PageElement\UserNav;

use Minextu\EttcUi\PageElement\AbstractPageElementModel;
use Minextu\Ettc\Account\Account;

class UserNavModel extends AbstractPageElementModel
{
    private $user;

    public function init()
    {
        $this->user = Account::checkLogin($this->mainModel->getDb());
    }

    /**
     * Checks if the User is logged in
     * @return   bool   True if the user is logged in, False otherwise
     */
    public function checkLogin()
    {
        if ($this->user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the nickname of the User
     * @return   bool|string   The nickname if the user is logged in, False otherwise
     */
    public function getNickname()
    {
        if ($this->user) {
            return $this->user->getNick();
        } else {
            return false;
        }
    }

    /**
     * Generates the URL for the users avatar
     * @return   string   Avatar image url
     */
    public function getAvatar()
    {
        $default = "http://img2.wikia.nocookie.net/__cb20110302033947/recipes/images/thumb/1/1c/Avatar.svg/480px-Avatar.svg.png";
        $size = 200;

        if ($this->user) {
            $avatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->user->getEmail()))) . "?d=" . urlencode($default) . "&s=" . $size . "&r=g";
        } else {
            $avatar = $default;
        }

        return $avatar;
    }
}
