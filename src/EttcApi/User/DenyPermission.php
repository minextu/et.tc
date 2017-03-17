<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Exception\InvalidId;
use Minextu\Ettc\Exception\InvalidName;

/**
 * Removes a permission for the given user, after checking for permissions
 *
 * @api {post} /user/denyPermission deny permission
 * @apiName denyPermission
 * @apiVersion 0.1.0
 * @apiGroup User
 *
 * @apiParam {int} userId         id of user to deny permission for
 * @apiParam {String} permission  Name of permission to deny
 *
 * @apiSuccess {bool} success     Status of the login
 *
 * @apiError NotLoggedIn          You are not logged in
 * @apiError NotFound             User with UserId not found
 * @apiError NoPermissions        No Permissions to deny Permissions
 * @apiError PermissionNotGranted You don't have the permssion yourself, that you are trying to deny
 * @apiError MissingValues        UserId or Permission wasn't transmitted
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 *
 **/

class DenyPermission extends AbstractRoutable
{
    /**
     * Removes a permission for the given user, after checking for permissions
     * @return   array   api answer
     */
    public function post()
    {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : false;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : false;

        $loggedin = $this->checkLoggedIn();
        $hasPermission = $this->checkPermission();
        $permissionGranted = $this->checkPermissionGranted($permission);

        if ($userId === false || empty($permission)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$hasPermission) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } elseif (!$permissionGranted) {
            http_response_code(403);
            $answer = ["error" => "PermissionNotGranted"];
        } else {
            $invalidId = false;
            try {
                $user = new User($this->getDb(), $userId);
            } catch (InvalidId $e) {
                $invalidId = true;
            }
            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $permissionObj = new Permission($this->getDb(), $user);
                $status = $permissionObj->deny($permission);
                $answer = ["success" => $status];
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

     /**
      * Checks if the user has the permission to grant permissions
      * @return   bool                   True if user has permissions, False otherwise
      */
     private function checkPermission()
     {
         $hasPermission = false;
         $user = Account::checkLogin($this->getDb());

         if ($user) {
             $permissionObj = new Permission($this->getDb(), $user);
             $hasPermission = $permissionObj->get("ettcApi/user/denyPermission");
         }

         return $hasPermission;
     }

     /**
      * Checks if the user has the permission himself, that he tries to grant
      * @param    String   $permission   Permission name to check
      * @return   bool                   True if user has permissions, False otherwise
      */
     private function checkPermissionGranted($permission)
     {
         $hasPermission = false;
         $user = Account::checkLogin($this->getDb());

         if ($user) {
             $permissionObj = new Permission($this->getDb(), $user);
             $hasPermission = $permissionObj->get($permission);
         }

         return $hasPermission;
     }
}
