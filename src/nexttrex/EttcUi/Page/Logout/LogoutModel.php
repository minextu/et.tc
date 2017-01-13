<?php namespace nexttrex\EttcUi\Page\Logout;
use \nexttrex\EttcUi\Page\AbstractPageModel;
use nexttrex\Ettc\Account\Account;

class LogoutModel extends AbstractPageModel
{
    public function logout()
    {
        return Account::logout();
    }
}
