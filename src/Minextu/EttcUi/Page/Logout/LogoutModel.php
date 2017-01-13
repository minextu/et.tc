<?php namespace Minextu\EttcUi\Page\Logout;
use \Minextu\EttcUi\Page\AbstractPageModel;
use Minextu\Ettc\Account\Account;

class LogoutModel extends AbstractPageModel
{
    /**
     * Logouts the User using the static class Account
     */
    public function logout()
    {
        return Account::logout();
    }
}
