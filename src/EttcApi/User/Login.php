<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\FailedLogin;

/**
 * Checks nickname and password, logs the user in on success
 *
 * @api        {post} /user/login/ login
 * @apiName    loginUser
 * @apiVersion 0.1.0
 * @apiGroup   User
 *
 * @apiParam {String} nickname            User nickname
 * @apiParam {String} password            User password
 *
 * @apiSuccess {bool} success             Status of the login
 *
 * @apiError        MissingValues           Nickname or Password weren't transmited
 * @apiError        AlreadyLoggedIn         You are already loggedin
 * @apiError        WrongNicknameOrPassword Password or nickname were wrong
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "WrongNicknameOrPassword"
 * }
 **/

class Login extends AbstractRoutable
{
    /**
     * Checks nickname and password and logs the user in
     *
     * @return array   api answer
     */
    public function post()
    {
        $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : false;
        $password = isset($_POST['password']) ? $_POST['password'] : false;

        $loggedin = $this->checkLoggedIn();
        $this->preventBruteForce($nickname);
        $loginCorrect = $this->checkLogin($nickname, $password);

        if (empty($nickname) || empty($password)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif ($loggedin) {
            http_response_code(409);
            $answer = ["error" => "AlreadyLoggedIn"];
        } elseif (!$loginCorrect) {
            http_response_code(401);
            $this->preventBruteForce($nickname, true);
            $answer = ["error" => "WrongNicknameOrPassword"];
        } else {
            $this->login($nickname);
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
     * Check if the username and password is correct
     *
     * @param  string $nick Nickname to check
     * @param  string $pw   Password to check
     * @return bool             True if nickname and password are correct, false otherwise
     */
    private function checkLogin(string $nick, string $pw)
    {
        $user = new User($this->getDb());

        // check username and password
        $status = $user->loadNick($nick);
        if ($status) {
            $status = $user->checkPassword($pw);
        }

        return $status;
    }

    /**
     * Sets the user to be logged in
     *
     * @param string $nick Nickname of the user
     */
    private function login(string $nick)
    {
        $user = new User($this->getDb());
        // load user
        $status = $user->loadNick($nick);

        // Set the status to logged in on success
        if ($status) {
            Account::login($user);
        } else {
            throw new Exception("User with Nick '$nick' not found");
        }
    }

    /**
     * Logs failed logins and add timeout when to many fail
     *
     * @param string  $nick          Nickname of the user
     * @param boolean $isFailedLogin Wether this login attempt has failed or not
     */
    private function preventBruteForce(string $nick, $isFailedLogin = false)
    {
        if (!$isFailedLogin) {
            $lastLoginAttempt = FailedLogin::getLastTime($this->getDb(), $nick);
            if ($lastLoginAttempt != false && time() - strtotime($lastLoginAttempt) < 60) {
                sleep(mt_rand(2, 5));
            }
        } else {
            FailedLogin::add($this->getDb(), $nick);
        }
    }
}
