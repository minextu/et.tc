<?php namespace Minextu\EttcApi\Rank;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Deletes a rank
 *
 * @api        {post} /rank/delete/:id delete a rank
 * @apiName    deleteRank
 * @apiVersion 0.1.0
 * @apiGroup   Rank
 *
 * @apiParam {Number} id                  Rank id
 *
 * @apiSuccess {bool} success             Status of the deletion
 *
 * @apiError        MissingValues Id wasn't transmited
 * @apiError        NotLoggedIn   You are not logged in
 * @apiError        NoPermissions No permissions to delete ranks
 * @apiError        NotFound      Rank couldn't be found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *   "error": "NoPermissions"
 * }
 **/

class Delete extends AbstractRoutable
{
    /**
     * Deletes the given rank, after checking for permissions
     *
     * @param  int $id rank id to be deleted
     * @return array           Api answers
     */
    public function post($id=false)
    {
        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $invalidId = false;
            try {
                $rank = new Rank($this->getDb(), $id);
            } catch (InvalidId $e) {
                $invalidId = true;
            }

            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $rank->delete();
                $answer = ["success" => true];
            }
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
            $hasPermissions = $permissionObj->get("ettcApi/rank/delete");
        }

        return $hasPermissions;
    }
}
