<?php namespace Minextu\EttcApi\User;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Generates a list of permissions a user (current if none is specified)
 *
 * @api {get} /user/permissions/:id list permissions
 * @apiName listPermissions
 * @apiVersion 0.1.0
 * @apiGroup User
 *
 * @apiParam {integer} [id]               User id to fetch permissions for (current user, if not specified)
 *
 * @apiSuccess {Array} items              Contains a list of permissions for this user
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "permissions" : String[]
 *     }
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No Permissions
 * @apiError NotFound      User with Id not found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 *
 **/

class PermissionList extends AbstractRoutable
{
    /**
     * Generate a list of Permissions for this user
     * @param    int       $id   user id to fetch permissions for
     * @return   array   List of permissions
     */
    public function get($id=false)
    {
        $loggedin = $this->checkLoggedIn();

        if (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } else {
            // show permissions for the loggedin user, if no one is specified
            if ($id === false || $id === "") {
                $currentUser = Account::checkLogin($this->getDb());
                $permissions = $this->getPermissions($currentUser);
                $answer = ["permissions" => $permissions];
            } else {
                $answer = $this->getPermissionsOtherUser($id);
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
      * Get all permissions for the given User
      * @param    \Ettc\Account\User  User to fetch permissions for
      * @return   String[]   All permissions for the current user
      */
     private function getPermissions($user)
     {
         $permission = new Permission($this->getDb());
         $permission->loadOnlyUser($user);

         $permissionList = $permission->getAll();

         return $permissionList;
     }

     /**
      * Get api answer for the given userId
      * @param    int   $id      User Id to fetch permissions for
      * @return   array          api answer
      */
     private function getPermissionsOtherUser($id)
     {
         $currentUser = Account::checkLogin($this->getDb());
         $permission = new Permission($this->getDb(), $currentUser);

         // check if the loggedin user has permissions to view permissions for other users
         if (!$permission->get("ettcApi/user/permissions:otherUsers")) {
             http_response_code(403);
             $answer = ["error" => "NoPermissions"];
         } else {
             // check if user with $id does exist
             $invalidId = false;
             try {
                 $user = new User($this->getDb(), $id);
             } catch (InvalidId $e) {
                 $invalidId = true;
             }
             if ($invalidId) {
                 http_response_code(404);
                 $answer = ["error" => "NotFound"];
             // show permissions for that user
             } else {
                 $permissions = $this->getPermissions($user);
                 $answer = ["permissions" => $permissions];
             }
         }

         return $answer;
     }
}
