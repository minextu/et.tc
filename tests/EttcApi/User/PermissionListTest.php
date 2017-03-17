<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
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

    public function testPermissionsCanBeListed()
    {
        $user = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $user);

        // create two dummy permissions
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $permissionsArray = $answer['permissions'];
        $this->assertEquals([$permission1Name, $permission2Name], $permissionsArray);
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
        $permission = new Permission($this->getDb(), $user);

        // create admin user, that will fetch these permissions
        $user = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/user/permissions:otherUsers");

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get(1);

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

        // create dummy permissions for that user
        $permission = new Permission($this->getDb(), $user);
        $permission1Name = "/phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "/phpUnit/dummyPermission2";
        $permission->grant($permission2Name);
        $permission = new Permission($this->getDb(), $user);

        // create admin user, that will fetch these permissions, do not add permission for fetching other users
        $user = $this->createLoginTestUser();

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get(1);

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
        $permission = new Permission($this->getDb(), $user);

        // create admin user, that will fetch these permissions
        $user = $this->createLoginTestUser();
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/user/permissions:otherUsers");

        // check if permissions get listed for wrong user id
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get(-1);

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotFound', $error);
    }

    public function testNoPermissionsExist()
    {
        $user = $this->createLoginTestUser();

        // check if permissions get listed
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permissions could not be listed (Error: $error)");

        $this->assertArrayHasKey("permissions", $answer, "Permissions weren't returned");

        $this->assertCount(0, $answer['permissions'], "No permissions should be returned");
    }

    public function testNotLoggedIn()
    {
        $permissionApi = new PermissionList($this->getDb());
        $answer = $permissionApi->get();
        $this->assertEquals('NotLoggedIn', $answer['error']);
    }
}
