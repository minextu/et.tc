<?php namespace Minextu\EttcApi\ApiKey;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\ApiKey;

/**
 * Generates a list of api keys for this user
 *
 * @api        {get} /apiKeys list api keys
 * @apiName    listApiKeys
 * @apiVersion 0.1.0
 * @apiGroup   ApiKey
 *
 * @apiSuccess {Array} items              Contains a list of api keys
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "items" : [
 *            {
 *               "id": Number,
 *               "title": String,
 *               "key" : String,
 *               "createDate" : Date,
 *               "lastUseDate" : Date
 *            }
 *         ]
 *     }
 * @apiError          NotLoggedIn   You are not logged in
 * @apiErrorExample   Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class ApiKeyList extends AbstractRoutable
{
    /**
     * Generate a list of Api keys for the logged in user
     *
     * @return array   List of api keys
     */
    public function get()
    {
        $loggedin = $this->checkLoggedIn();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            $apiKeys = $this->getApiKeys();
            $answer = ["items" => $apiKeys];
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
     * Get all api keys for this user, convert them to arrays
     *
     * @return array   all api keys as arrays
     */
    private function getApiKeys()
    {
        $user =  Account::checkLogin($this->getDb());
        $apiKeys = ApiKey::getAll($this->getDb(), $user);

        $keysArray = [];
        foreach ($apiKeys as $key) {
            $array = $key->toArray();
            $keysArray[] = $array;
        }

        return $keysArray;
    }
}
