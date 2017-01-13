<?php namespace Minextu\EttcUi\Page\Login;
use \Minextu\EttcUi\Page\AbstractPageModel;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\User;
use Minextu\EttcUi\Exception;

class LoginModel extends AbstractPageModel
{
    /**
     * Check if the username and password is correct
     * @param    string   $nick   Nickname to check
     * @param    string   $pw     Password to check
     * @return   bool             True if nickname and password are correct, false otherwise
     */
    public function checkLogin($nick, $pw)
    {
        $user = new User($this->mainModel->getDb());

        // check username and password
        $status = $user->loadNick($nick);
        if ($status)
            $status = $user->checkPassword($pw);

        return $status;
    }

    /**
     * Sets the user to be logged in
     * @param    string   $nick   Nickname of the user
     */
    public function login($nick)
    {
        $user = new User($this->mainModel->getDb());
        // load user
        $status = $user->loadNick($nick);

        // Set the status to logged in on success
        if ($status)
            Account::login($user);
        else
            throw new Exception("User with Nick '$nick' not found");
    }
}
