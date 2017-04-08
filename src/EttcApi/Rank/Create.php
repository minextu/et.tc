<?php namespace Minextu\EttcApi\Rank;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Account\Rank;

/**
 * Creates a new rank
 *
 * @api        {post} /rank/create/ create rank
 * @apiName    createRank
 * @apiVersion 0.1.0
 * @apiGroup   Rank
 *
 * @apiParam {String} [title]                 title for the new rank
 *
 * @apiSuccess {Boolean} success              Status of rank creation
 * @apiSuccess {Object}  rank                 Info about the created rank
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "success" : Boolean,
 *         "rank" : {
 *              "id" : int,
 *              "title" : String
 *         }
 *     }
 *
 * @apiError NotLoggedIn   You are not logged in
 * @apiError MissingValues Title or description weren't transmited
 * @apiError NoPermissions No permissions to create a rank
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class Create extends AbstractRoutable
{
    /**
     * Creates a new rank if user has permissions
     *
     * @return array   api answer
     */
    public function post()
    {
        $title = isset($_POST['title']) ? $_POST['title'] : false;

        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if (empty($title)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $rank = $this->createRank($title);
            $answer = ["success" => true, "rank" => $rank];
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
      * Check if the current user has permissions
      *
      * @return bool   True if the user has permissions, False otherwise
      */
    private function checkPermissions()
    {
        $hasPermissions = false;
        $user = Account::checkLogin($this->getDb());

        // only proceed if logged in
        if ($user) {
            $permissionObj = new Permission($this->getDb(), $user);
            $hasPermissions = $permissionObj->get("ettcApi/rank/create");
        }

        return $hasPermissions;
    }

    /**
     * Creates a new rank
     *
     * @param string $title Title for the rank
     */
    private function createRank(string $title)
    {
        $rank = new Rank($this->getDb());
        $rank->setTitle($title);
        $rank->create();

        return $rank->toArray();
    }
}
