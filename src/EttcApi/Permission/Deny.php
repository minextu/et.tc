<?php namespace Minextu\EttcApi\Permission;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Exception\InvalidId;
use Minextu\Ettc\Exception\InvalidName;

/**
 * Denies a permission for the given user, rank or api key, after checking for permissions
 *
 * @api        {post} /permission/deny deny permission
 * @apiName    denyPermission
 * @apiVersion 0.1.0
 * @apiGroup   Permission
 *
 * @apiParam {int} entityId                              id of user, rank or api key to deny permission for
 * @apiParam {String=user,rank,apiKey} entityType  Type of entity
 * @apiParam {String} permission                         Name of permission to deny
 *
 * @apiSuccess {bool} success     Status
 *
 * @apiError NotLoggedIn          You are not logged in
 * @apiError NotFound             Entity with entityId not found
 * @apiError NoPermissions        No Permissions to deny Permissions
 * @apiError PermissionNotGranted You don't have the permssion yourself, that you are trying to deny
 * @apiError MissingValues        entityId, entityType or permission wasn't transmitted
 * @apiError InvalidValues        entityType contains an invalid value
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 401 Unauthorized
 * {
 *    "error": "NotLoggedIn"
 * }
 **/

class Deny extends AbstractRoutable
{
    /**
     * Denies a permission for the given entity, after checking for permissions
     *
     * @return array   api answer
     */
    public function post()
    {
        $entityId = isset($_POST['entityId']) ? $_POST['entityId'] : false;
        $entityType = isset($_POST['entityType']) ? $_POST['entityType'] : false;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : false;

        $allowedEntityType = ["user", "rank", "apiKey"];

        $loggedin = $this->checkLoggedIn();
        $hasPermission = $this->checkPermission($entityType);
        $permissionGranted = $this->checkPermissionGranted($permission);

        if ($entityId === false || empty($entityType) || empty($permission)) {
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
        } elseif (!$permissionGranted) {
            http_response_code(403);
            $answer = ["error" => "PermissionNotGranted"];
        } else {
            $entity = $this->createEntity($entityId, $entityType);
            if (!$entity) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $answer = $this->denyPermission($entity, $entityType, $permission);
            }
        }

        return $answer;
    }

    /**
     * Creates the given entity
     *
     * @param  int    $entityId   Id of the entity
     * @param  String $entityType Type of the entity
     * @return \Minextu\Ettc\Account\User|\Minextu\Ettc\Account\Rank|\Minextu\Ettc\Account\ApiKey     The created Entity or False if not found
     */
    private function createEntity($entityId, $entityType)
    {
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
     * Denies permission for the given entity
     *
     * @param  \Minextu\Ettc\Account\User|\Minextu\Ettc\Account\Rank|\Minextu\Ettc\Account\ApiKey $entity     Entity to deny permissions for
     * @param  String                                                                             $entityType Type of the entity
     * @param  String                                                                             $permission Permission to deny
     * @return String[]               Api answer
     */
    private function denyPermission($entity, $entityType, $permission)
    {
        $permissionObj = new Permission($this->getDb());

        if ($entityType == "user") {
            $permissionObj->loadOnlyUser($entity);
        } elseif ($entityType == "rank") {
            $permissionObj->loadRank($entity);
        } elseif ($entityType == "apiKey") {
            $permissionObj->loadApiKey($entity);
        }

        try {
            $status = $permissionObj->deny($permission);
            $answer = ["success" => $status];
        } catch (InvalidName $e) {
            http_response_code(400);
            $answer = ["error" => "InvalidName", "errorText" => $e->getMessage()];
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
      * Checks if the user has the permission to deny permissions for this entity
      *
      * @param  String $entityType Type of the entity
      * @return bool                   True if user has permissions, False otherwise
      */
    private function checkPermission($entityType)
    {
        $hasPermission = false;
        $user = Account::checkLogin($this->getDb());

        if ($user) {
            $permissionObj = new Permission($this->getDb(), $user);
            $hasPermission = $permissionObj->get("ettcApi/permission/deny:$entityType");
        }

        return $hasPermission;
    }

     /**
      * Checks if the user has the permission himself, that he tries to grant
      *
      * @param  String $permission Permission name to check
      * @return bool                   True if user has permissions, False otherwise
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
