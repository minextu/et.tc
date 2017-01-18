<?php namespace Minextu\EttcUi\Page\Login;

use Minextu\EttcUi\Page\AbstractPageModel;
use Minextu\Ettc\Account\User;
use Minextu\EttcApi\User\Login;
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
        if ($status) {
            $status = $user->checkPassword($pw);
        }

        return $status;
    }

    /**
     * Sets the user to be logged in using the ettc api
     * @param    string   $nick   Nickname of the user
     * @param    string   $pw     Password of the user
     */
    public function login($nick, $pw)
    {
        $_POST['nickname'] = $nick;
        $_POST['password'] = $pw;

        $loginApi = new Login($this->mainModel->getDb());
        $answer = $loginApi->post();

        if (isset($answer['error'])) {
            throw new Exception($answer['error']);
        } else {
            return true;
        }
    }
}
