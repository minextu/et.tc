<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Deletes an Api Key for this user
 *
 * @api {post} /user/deleteApiKey/:id delete an api key
 * @apiName deleteApiKey
 * @apiVersion 0.1.0
 * @apiGroup User
 *
 * @apiParam {Number} id                  Api key id
 *
 * @apiSuccess {bool} success             Status of the deletion
 *
 * @apiError MissingValues Id wasn't transmited
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No permissions to delete this api key
 * @apiError NotFound      Api key couldn't be found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *   "error": "NoPermissions"
 * }
 **/

class DeleteApiKey extends AbstractRoutable
{
    /**
     * Deletes the given api key, after checking for permissions
     * @param    int       $id   Api key id to be deleted
     * @return   array           Api answers
     */
    public function post($id=false)
    {
        $loggedin = $this->checkLoggedIn();

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            $invalidId = false;
            try {
                $apiKey = new ApiKey($this->getDb(), $id);
            } catch (InvalidId $e) {
                $invalidId = true;
            }

            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                if ($apiKey->getUser()->getId() != Account::checkLogin($this->getDb())->getId()) {
                    http_response_code(403);
                    $answer = ["error" => "NoPermissions"];
                } else {
                    $apiKey->delete();
                    $answer = ["success" => true];
                }
            }
        }

        return $answer;
    }

    /**
     * Check the current login status
     * @return   bool   True if the user ist logged in, False otherwise
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
}
