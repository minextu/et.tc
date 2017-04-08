<?php namespace Minextu\EttcApi\ApiKey;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\ApiKey;

/**
 * Adds a new api key for the logged in user
 *
 * @api        {post} /apiKey/create/ create api key
 * @apiName    createApiKey
 * @apiVersion 0.1.0
 * @apiGroup   ApiKey
 *
 * @apiParam {String} [title]                title for the new key
 *
 * @apiSuccess {Boolean} success              Status of key creation
 * @apiSuccess {Object}  key                  Info about the created key
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "success" : Boolean,
 *         "key" : {
 *              "id" : int,
 *              "title" : String,
 *              "key" : String,
 *               "createDate" : Date,
 *               "lastUseDate" : Date
 *         }
 *     }
 *
 * @apiError        NotLoggedIn   You are not logged in
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class Create extends AbstractRoutable
{
    /**
     * Adds a new api key if user is logged in
     *
     * @return array   api answer
     */
    public function post()
    {
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $loggedin = $this->checkLoggedIn();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            $key = $this->createApiKey($title);
            $answer = ["success" => true, "key" => $key];
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
      * Generates a new api key
      *
      * @param string $title Title for the key
      */
    private function createApiKey($title)
    {
        $user = Account::checkLogin($this->getDb());

        $apiKey = new ApiKey($this->getDb());
        $apiKey->setUser($user);
        $apiKey->setTitle($title);
        $apiKey->create();

        return $apiKey->toArray();
    }
}
