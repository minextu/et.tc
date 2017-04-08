<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\Exception;

/**
  * Can Create, Delete and fetch permissions for a user
  */
class Permission
{
    /**
     * Contains generic permissions used throughout ettc
     * @var   String[]
     */
    private $allPermissions = [

        // api: project
        "ettcApi/project/create",
        "ettcApi/project/delete",
        "ettcApi/project/initGit",
        "ettcApi/project/update",

        // api: rank
        "ettcApi/ranks",
        "ettcApi/rank/create",
        "ettcApi/rank/delete",

        // api: user
        "ettcApi/user/grantPermission",
        "ettcApi/user/denyPermission",
        "ettcApi/user/permissions:otherUsers"
    ];

    /**
     * Permission Database Interface
     * @var PermissionDb
     */
    private $permissionDb;

    /**
     * User/Rank or ApiKey for this permissions
     * @var   User|ApiKey|Rank
     */
    private $entity;

    /**
     * Type of permission entity (user, apikey or rank)
     * @var   String
     */
    private $entityType;

    /**
     * Contains status for all permissions. Array key is permission name
     * @var   Bool[]
     */
    private $permissions = [];

    /**
     * Loads permissions for the given user
     * @param   Database\DatabaseInterface   $db   Database to be used
     * @param   User   $user                       User to fetch permissions for (will also include the users rank)
     */
    public function __construct($db, $user=false)
    {
        $this->permissionDb = new PermissionDb($db);

        if ($user !== false) {
            // try to load permissions for users rank
            try {
                $rank = new Rank($db, $user->getRank());
                $this->loadRank($rank);
            } catch (Exception\InvalidId $e) {
            }

            // load users permissions
            $this->loadOnlyUser($user, true);
        }
    }

    /**
     * Load permissions for the given user
     * @param    User   $user     User to fetch permissions for
     * @param    bool   $keepPreviousPermissions Wether to delte previous loaded permissions or not
     */
    public function loadOnlyUser($user, $keepPreviousPermissions=false)
    {
        if (!$keepPreviousPermissions) {
            $this->permissions = [];
        }

        // get permission csv from database
        $userId = $user->getId();
        $permissionsString = $this->permissionDb->getPermissionsByUserId($userId);

        // load permissions
        $this->loadPermissionCsv($permissionsString);

        // save user to entity
        $this->entity = $user;
        $this->entityType = "user";
    }


    /**
     * Load permissions for the given rank
     * @param    Rank   $rank     Rank to fetch permissions for
     * @param    bool   $keepPreviousPermissions Wether to delte previous loaded permissions or not
     */
    public function loadRank($rank, $keepPreviousPermissions=false)
    {
        if (!$keepPreviousPermissions) {
            $this->permissions = [];
        }

        // get permission csv from database
        $rankId = $rank->getId();
        $permissionsString = $this->permissionDb->getPermissionsByRankId($rankId);

        // load permissions
        $this->loadPermissionCsv($permissionsString);

        // save rank to entity
        $this->entity = $rank;
        $this->entityType = "rank";
    }

    /**
     * Loads permissions out of a csv string
     * @param    String   $permissionString   Csv list of permissions
     */
    private function loadPermissionCsv($permissionString)
    {
        // convert csv to assoc array
        $permissionsArray = explode(",", $permissionString);
        foreach ($permissionsArray as $permission) {
            // save permission
            if (!empty($permission)) {
                $this->permissions[$permission] = true;
            }
        }
    }

    /**
     * Count all granted permissions for this user
     * @return   int   Number of granted permissions
     */
    public function count()
    {
        return count($this->permissions);
    }

    /**
     * Grant permission with the given name
     * @param   String   $permissionName   Name of the permission to grant
     */
    public function grant($permissionName)
    {
        if (strpos($permissionName, ",") !== false) {
            throw new Exception\InvalidName("Permission must not contain a comma");
        }
        if (empty($permissionName)) {
            throw new Exception\InvalidName("Permission must not be empty");
        }

        $this->permissions[$permissionName] = true;
        $this->savePermissions();
        return true;
    }

    /**
     * Deny permission with the given name
     * @param    String   $permissionName   Name of the permission to deny
     * @return   Bool                       True on success, False otherwise
     */
    public function deny($permissionName)
    {
        unset($this->permissions[$permissionName]);
        $this->savePermissions();
        return true;
    }

    /**
     * Check if the given permission is granted
     * @param    String   $permissionName   Name of the permission to Check
     * @return   bool                       True if permissions is granted, False otherwise
     */
    public function get($permissionName)
    {
        $hasPermission = false;

        // check if permission is in permissions array and set to true
        if (!empty($this->permissions[$permissionName])) {
            $hasPermission = true;
        // always return true, when the permission "all" is set to true
        } elseif (!empty($this->permissions["all"])) {
            $hasPermission = true;
        }

        return $hasPermission;
    }

    /**
     * Get all granted Permissions for this user
     * @return   String[]   Names of all granted Permissions for this user
     */
    public function getAll()
    {
        $permissionArray = [];
        foreach ($this->permissions as $permission => $status) {
            if ($status) {
                $permissionArray[] = $permission;
            }
        }
        return $permissionArray;
    }

    /**
     * Get all available generic permissions that are used throughout ettc
     * @return   String[]   Names of all available generic permissions
     */
    public function getAllAvailable()
    {
        return $this->allPermissions;
    }

    /**
     * Update permissions array on database
     */
    private function savePermissions()
    {
        $permissionString = "";
        foreach ($this->permissions as $permission => $status) {
            if ($status) {
                $permissionString .= $permission . ",";
            }
        }

        if ($this->entityType === "user") {
            $this->permissionDb->updatePermissionsForUser($this->entity->getId(), $permissionString);
        } elseif ($this->entityType == "rank") {
            $this->permissionDb->updatePermissionsForRank($this->entity->getId(), $permissionString);
        } else {
            throw new Exception\Exception("Invalid entityType '$entityType'");
        }
    }
}
