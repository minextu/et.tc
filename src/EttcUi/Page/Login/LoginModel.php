<?php namespace Minextu\EttcUi\Page\Login;

use Minextu\EttcUi\Page\AbstractPageModel;
use Minextu\Ettc\Account\User;
use Minextu\EttcApi\User\Login;
use Minextu\EttcUi\Exception;

class LoginModel extends AbstractPageModel
{
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
