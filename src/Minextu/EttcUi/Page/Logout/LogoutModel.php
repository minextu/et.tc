<?php namespace Minextu\EttcUi\Page\Logout;

use \Minextu\EttcUi\Page\AbstractPageModel;
use Minextu\Ettc\Account\Account;
use Minextu\EttcApi\User\Logout;
use Minextu\EttcUi\Exception;

class LogoutModel extends AbstractPageModel
{
    /**
     * Logouts the User using the ettc api
     */
    public function logout()
    {
        $logoutApi = new Logout($this->mainModel->getDb());
        $answer = $logoutApi->post();

        if (isset($answer['error'])) {
            throw new Exception($answer['error']);
        } else {
            return true;
        }
    }
}
