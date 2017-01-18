<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;

/**
 * @api {post} /user/logout/ logout
 * @apiName logoutUser
 * @apiVersion 0.1.0
 * @apiGroup User
 *
 * @apiSuccess {bool} success             Status of the logout
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *          "success" => true
 *     }
 *
 * @apiError NotLoggedIn   You are already loggedin
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class Logout extends AbstractRoutable
{
    public function post()
    {
        $loggedin = $this->checkLoginStatus();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            $this->logout();
            $answer = ["success" => true];
        }

        return $answer;
    }

    private function checkLoginStatus()
    {
        $loggedin = false;
        $user = Account::checkLogin($this->ettc->getDb());

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
