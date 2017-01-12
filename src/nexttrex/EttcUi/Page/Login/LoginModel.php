<?php namespace nexttrex\EttcUi\Page\Login;
use \nexttrex\EttcUi\Page\AbstractPageModel;
use nexttrex\Ettc\Account\Account;
use nexttrex\Ettc\Account\User;

class LoginModel extends AbstractPageModel
{
    public function checkLogin($nick, $pw)
    {
        $user = new User($this->mainModel->getDb());

        // check username and password
        $status = $user->loadNick($nick);
        if ($status)
            $status = $user->checkPassword($pw);

        // Set the status to logged in on success
        if ($status)
            Account::login($user);

        return $status;
    }
}
