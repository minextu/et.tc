<?php namespace Minextu\EttcApi\Permission;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;

class PermissionListTest extends AbstractEttcDatabaseTest
{
    private function createLoginTestUser()
    {
        $user = new User($this->getDb());

        // save user
        $nickname = "phpUnit_Test_User";
        $email = "testuser@nexttrex.de";
        $password = "abc123";

        $user->setNick($nickname);
        $user->setEmail($email);
        $user->setPassword($password);

        $user->create();
        Account::login($user, $this->getDb());
        return $user;
    }

    public function testPermissionsCanBeListedForTheCurrentUser()
    {
        $user = $this->createLoginTestUser();

        // grant permissions to view own permissions
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/permission/list:user:own");

        // create two dummy permissions
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $user->getId();
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals(["ettcApi/permission/list:user:own", $permission1Name, $permission2Name], $permissionsArray);
    }

    public function testPermissionsForOtherUsersCanBeListed()
    {
        // create test user, to fetch permissions for
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create dummy permissions for that user
        $permission = new Permission($this->getDb(), $user);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $user->getId();
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
    }

    public function testNoPermissionsForOtherUsers()
    {
        // create test user, to fetch permissions for
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create admin user, that will fetch these permissions, do not add permission for fetching other users
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:own");
        #$permission->grant("ettcApi/permission/list:user:other");
        $permission->grant("ettcApi/permission/list:rank:own");
        $permission->grant("ettcApi/permission/list:rank:other");
        $permission->grant("ettcApi/permission/list:apiKey:own");
        $permission->grant("ettcApi/permission/list:apiKey:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $user->getId();
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoPermissions', $error);
    }

    public function testNoPermissionsForOtherRanks()
    {
        // create test rank, to fetch permissions for
        $rank = new Rank($this->getDb());
        $rank->setTitle("Test");
        $rank->create();

        // create admin user, that will fetch these permissions, do not add permission for fetching other ranks
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:own");
        $permission->grant("ettcApi/permission/list:user:other");
        $permission->grant("ettcApi/permission/list:rank:own");
        #$permission->grant("ettcApi/permission/list:rank:other");
        $permission->grant("ettcApi/permission/list:apiKey:own");
        $permission->grant("ettcApi/permission/list:apiKey:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $rank->getId();
        $_GET['entityType'] = "rank";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoPermissions', $error);
    }

    public function testNoPermissionsForOtherApiKeys()
    {
        // create test api key, to fetch permissions for
        $apiKeyUser = new User($this->getDb());
        $apiKeyUser->setNick("Test");
        $apiKeyUser->setPassword("abc123");
        $apiKeyUser->create();

        $apiKey = new ApiKey($this->getDb());
        $apiKey->setTitle("Test");
        $apiKey->setUser($apiKeyUser);
        $apiKey->create();

        // create admin user, that will fetch these permissions, do not add permission for fetching other api keys
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:own");
        $permission->grant("ettcApi/permission/list:user:other");
        $permission->grant("ettcApi/permission/list:rank:own");
        $permission->grant("ettcApi/permission/list:rank:other");
        $permission->grant("ettcApi/permission/list:apiKey:own");
        #$permission->grant("ettcApi/permission/list:apiKey:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $apiKey->getId();
        $_GET['entityType'] = "apiKey";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoPermissions', $error);
    }

    public function testWrongUserId()
    {
        // create test user, to fetch permissions for
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create dummy permissions for that user
        $permission = new Permission($this->getDb(), $user);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // create admin user, that will fetch these permissions
        $user = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/user/listPermissions:otherUsers");

        // check if permissions get listed for wrong user id
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = -1;
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotFound', $error);
    }

    public function testEmptyPermissions()
    {
        // create test user, to fetch permissions for
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $user->getId();
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $this->assertCount(0, $answer['permissions'], "No permissions should be returned");
    }

    public function testNotLoggedIn()
    {
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = 1;
        $_GET['entityType'] = "user";
        $answer = $permissionApi->get();

        $this->assertEquals('NotLoggedIn', $answer['error']);
    }

    public function testMissingEntityId()
    {
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        // try to list permission with a missing entity id
        $_GET['entityType'] = "user";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('MissingValues', $error);
    }

    public function testWrongEntityType()
    {
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User2";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:user:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        // try to list permission with an invalid entity type defined
        $_GET['entityId'] = $user->getId();
        $_GET['entityType'] = "invalid";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('InvalidValues', $error);
    }

    public function testPermissionsForOtherRanksCanBeListed()
    {
        // create test rank, to fetch permissions for
        $rank = new Rank($this->getDb());
        $rank->setTitle("Test");
        $rank->create();

        // create dummy permissions for that rank
        $permission = new Permission($this->getDb());
        $permission->loadRank($rank);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:rank:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $rank->getId();
        $_GET['entityType'] = "rank";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
    }

    public function testPermissionsForOtherApiKeysCanBeListed()
    {
        // create test api key, to fetch permissions for
        $apiKeyUser = new User($this->getDb());
        $apiKeyUser->setNick("Test");
        $apiKeyUser->setPassword("abc123");
        $apiKeyUser->create();

        $apiKey = new ApiKey($this->getDb());
        $apiKey->setTitle("Test");
        $apiKey->setUser($apiKeyUser);
        $apiKey->create();

        // create dummy permissions for that api key
        $permission = new Permission($this->getDb());
        $permission->loadApiKey($apiKey);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // create admin user, that will fetch these permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:apiKey:other");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $apiKey->getId();
        $_GET['entityType'] = "apiKey";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
    }

    public function testPermissionsForOwnRanksCanBeListed()
    {
        // create test rank, to fetch permissions for
        $rank = new Rank($this->getDb());
        $rank->setTitle("Test");
        $rank->create();

        // create dummy permissions for that rank
        $permission = new Permission($this->getDb());
        $permission->loadRank($rank);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // create admin user, that will fetch these permissions
        $admin = new User($this->getDb());
        $admin->setNick("phpUnit_Test_User");
        $admin->setPassword("abc123");

        // assign admin to the rank
        $admin->setRank($rank->getId());

        // create and log in admin
        $admin->create();
        Account::login($admin, $this->getDb());

        // set permissions for admin
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:rank:own");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $rank->getId();
        $_GET['entityType'] = "rank";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
    }

    public function testPermissionsForOwnApiKeysCanBeListed()
    {
        // create admin user, that will fetch the permissions
        $admin = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $admin);
        $permission->grant("ettcApi/permission/list:apiKey:own");

        // create test api key, to fetch permissions for
        $apiKey = new ApiKey($this->getDb());
        $apiKey->setTitle("Test");
        $apiKey->setUser($admin);
        $apiKey->create();

        // create dummy permissions for that api key
        $permission = new Permission($this->getDb());
        $permission->loadApiKey($apiKey);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());

        $_GET['entityId'] = $apiKey->getId();
        $_GET['entityType'] = "apiKey";

        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
    }
}
