<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;

/**
 * Logout the user
 *
 * @api        {post} /user/logout/ logout
 * @apiName    logoutUser
 * @apiVersion 0.1.0
 * @apiGroup   User
 *
 * @apiSuccess {bool} success             Status of the logout

 * @apiError        NotLoggedIn   You are not logged in
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class Logout extends AbstractRoutable
{
    /**
     * Logout the user
     *
     * @return array   api answer
     */
    public function post()
    {
        $loggedin = $this->checkLoggedIn();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            $this->logout();
            $answer = ["success" => true];
        }

        return $answer;
    }

    /**
     * Check the current login status
     *
     * @return bool   True if the user ist logged in, False otherwise
     */
    private function checkLoggedIn()
    {
        $loggedin = false;
        $user = Account::checkLogin($this->getDb());

        if ($user) {
            $loggedin = true;
        }

        return $loggedin;
    }

    /**
     * Logouts the User using the static class Account
     */
    private function logout()
    {
        Account::logout();
    }
}
