<?php namespace Minextu\EttcApi\Permission;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Generates a list of permissions a user, rank or apikey
 *
 * @api {get} /permission/list   list permissions
 * @apiName listPermissions
 * @apiVersion 0.1.0
 * @apiGroup Permission
 *
 * @apiParam {integer} entityId]                   User, Rank or Api key id to fetch permissions for
 * @apiParam {String=user,rank,apiKey} entityType  Type of entity
 * @apiSuccess {Array} items                       Contains a list of permissions for this entity
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "permissions" : String[]
 *     }
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No Permissions
 * @apiError NotFound      Entity with enityId not found
 * @apiError MissingValues entityId or entityType wasn't transmitted
 * @apiError InvalidValues entityType contains an invalid value
 *
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
     * Generate a list of Permissions for an entity
     * @return   array   List of permissions
     */
    public function get()
    {
        $entityId = isset($_GET['entityId']) ? $_GET['entityId'] : false;
        $entityType = isset($_GET['entityType']) ? $_GET['entityType'] : false;

        $allowedEntityType = ["user", "rank", "apiKey"];

        $loggedin = $this->checkLoggedIn();
        $hasPermission = $this->checkPermission($entityId, $entityType);

        if ($entityId === false || empty($entityType)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!in_array($entityType, $allowedEntityType)) {
            http_response_code(400);
            $answer = ["error" => "InvalidValues"];
        } elseif (!$loggedin) {
            http_response_code(401);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$hasPermission) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $entity = $this->createEntity($entityId, $entityType);
            if (!$entity) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $answer = $this->listPermission($entity, $entityType);
            }
        }

        return $answer;
    }

    /**
     * Creates the given entity
     * @param    int   $entityId     Id of the entity
     * @param    String   $entityType   Type of the entity
     * @return   \Minextu\Ettc\Account\User|\Minextu\Ettc\Account\Rank|\Minextu\Ettc\Account\ApiKey     The created Entity or False if not found
     */
    private function createEntity($entityId, $entityType)
    {
        $entity = false;

        if ($entityId === false) {
            return false;
        }

        try {
            if ($entityType == "user") {
                $entity = new User($this->getDb(), $entityId);
            } elseif ($entityType == "rank") {
                $entity = new Rank($this->getDb(), $entityId);
            } elseif ($entityType == "apiKey") {
                $entity = new ApiKey($this->getDb(), $entityId);
            }
        } catch (InvalidId $e) {
            $entity = false;
        }

        return $entity;
    }

    /**
     * Lists permission for the given entity while checking for permissions
     * @param    \Minextu\Ettc\Account\User|\Minextu\Ettc\Account\Rank|\Minextu\Ettc\Account\ApiKey   $entity  Entity to list permissions for
     * @param    String   $entityType   Type of the entity
     * @return   String[]               Api answer
     */
    private function listPermission($entity, $entityType)
    {
        $permissionObj = new Permission($this->getDb());

        if ($entityType == "user") {
            $permissionObj->loadOnlyUser($entity);
        } elseif ($entityType == "rank") {
            $permissionObj->loadRank($entity);
        } elseif ($entityType == "apiKey") {
            $permissionObj->loadApiKey($entity);
        }

        $answer = ["permissions" => $permissionObj->getAll() ];

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
      * Checks if the user has the permission to list permissions for this entity
      * @param    String   $entityType   Type of the entity
      * @return   bool                   True if user has permissions, False otherwise
      */
     private function checkPermission($entityId, $entityType)
     {
         $hasPermission = false;
         $user = Account::checkLogin($this->getDb());

         if ($user) {
             // check if entity is foreign to the user
             $foreign = "other";
             $entity = $this->createEntity($entityId, $entityType);

             if (!$entity) {
                 $hasPermission = true;
             } else {
                 // if entity is a rank, check if user is in this rank
                 if ($entityType == "rank" && $user->getRank() == $entity->getId()) {
                     $foreign = "own";
                 }
                 // if entity is an apiKey, check if it was created by this user
                 elseif ($entityType == "apiKey" && $entity->getUser()->getId() == $user->getId()) {
                     $foreign = "own";
                 }
                 // if entity is a user, check if it is the user itself
                 elseif ($entityType == "user" && $entity->getId() == $user->getId()) {
                     $foreign = "own";
                 }

                 $permissionObj = new Permission($this->getDb(), $user);
                 $hasPermission = $permissionObj->get("ettcApi/permission/list:$entityType:$foreign");
             }
         }

         return $hasPermission;
     }
}
