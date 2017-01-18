<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\Ettc;
use Respect\Rest\Routable;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;

/**
 * @api {post} /user/login/ login
 * @apiName loginUser
 * @apiVersion 0.1.0
 * @apiGroup User
 *
 * @apiParam {String} nickname            User nickname
 * @apiParam {String} password            User password
 *
 * @apiSuccess {bool} success             Status of the login
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *          "success" => true
 *     }
 *
 * @apiError MissingValues     Nickname or Password weren't transmited
 * @apiError AlreadyLoggedIn   You are already loggedin
 * @apiError WrongNicknameOrPassword Password or nickname were wrong
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "WrongNicknameOrPassword"
 * }
 **/

class Login implements Routable
{
    public function post()
    {
        $ettc = new Ettc();

        $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : false;
        $password = isset($_POST['password']) ? $_POST['password'] : false;

        $loggedin = $this->checkLoginStatus($ettc);
        $loginCorrect = $this->checkLogin($ettc, $nickname, $password);

        if (empty($nickname) || empty($password)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif ($loggedin) {
            http_response_code(409);
            $answer = ["error" => "AlreadyLoggedIn"];
        } elseif (!$loginCorrect) {
            http_response_code(401);
            $answer = ["error" => "WrongNicknameOrPassword"];
        } else {
            $this->login($ettc, $nickname);
            $answer = ["success" => true];
        }

        return $answer;
    }

    private function checkLoginStatus($ettc)
    {
        $loggedin = false;
        $user = Account::checkLogin($ettc->getDb());

        if ($user) {
            $loggedin = true;
        }

        return $loggedin;
    }

    /**
     * Check if the username and password is correct
     * @param    string   $nick   Nickname to check
     * @param    string   $pw     Password to check
     * @return   bool             True if nickname and password are correct, false otherwise
     */
    public function checkLogin($ettc, $nick, $pw)
    {
        $user = new User($ettc->getDb());

        // check username and password
        $status = $user->loadNick($nick);
        if ($status) {
            $status = $user->checkPassword($pw);
        }

        return $status;
    }

    /**
     * Sets the user to be logged in
     * @param    string   $nick   Nickname of the user
     */
    public function login($ettc, $nick)
    {
        $user = new User($ettc->getDb());
        // load user
        $status = $user->loadNick($nick);

        // Set the status to logged in on success
        if ($status) {
            Account::login($user);
        } else {
            throw new Exception("User with Nick '$nick' not found");
        }
    }
}
