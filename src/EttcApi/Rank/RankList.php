<?php namespace Minextu\EttcApi\Rank;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Account\Rank;

/**
 * Generates a list of ranks
 *
 * @api {get} /ranks list ranks
 * @apiName listRanks
 * @apiVersion 0.1.0
 * @apiGroup Rank
 *
 * @apiSuccess {Array} items              Contains a list of ranks
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "items" : [
 *            {
 *               "id": Number,
 *               "title": String
 *            }
 *         ]
 *     }
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No permissions to list ranks
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 *
 **/

class RankList extends AbstractRoutable
{
    /**
     * Generate a list of ranks
     * @return   array   List of ranks
     */
    public function get()
    {
        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $ranks = $this->getRanks();
            $answer = ["items" => $ranks];
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

     /**
      * Check if the current user has permissions
      * @return   bool   True if the user has permissions, False otherwise
      */
     private function checkPermissions()
     {
         $hasPermissions = false;
         $user = Account::checkLogin($this->getDb());

         // only proceed if logged in
         if ($user) {
             $permissionObj = new Permission($this->getDb(), $user);
             $hasPermissions = $permissionObj->get("ettcApi/ranks");
         }

         return $hasPermissions;
     }

    /**
     * Get all ranks, convert them to arrays
     * @return   array   all ranks as arrays
     */
    private function getRanks()
    {
        $ranks = Rank::getAll($this->getDb());

        $ranksArray = [];
        foreach ($ranks as $rank) {
            $ranksArray[] = $rank->toArray();
        }

        return $ranksArray;
    }
}
