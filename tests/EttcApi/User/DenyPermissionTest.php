<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;

class DenyPermissionTest extends AbstractEttcDatabaseTest
{
    private function createTestUser($nickname)
    {
        $user = new User($this->getDb());

        // save user
        $password = "abc123";

        $user->setNick($nickname);
        $user->setPassword($password);

        $user->create();
        return $user;
    }

    public function testPermissionCanBeDenied()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be removed
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/user/denyPermission");
        $permission->grant($permissionName);
        Account::login($user2);

        // deny dummy permission for user1
        $_POST['userId'] = $user1->getId();
        $_POST['permission'] = $permissionName;

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permission could not be removed (Error: $error)");
        $this->assertEquals(['success' => true], $answer, "Permission wasn't removed successfull");

        // check if permission was removed
        $permission = new Permission($this->getDb(), $user1);
        $hasPermission = $permission->get($permissionName);
        $this->assertFalse($hasPermission, "Permission wasn't removed");
    }

    public function testNoPermissions()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be removed
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and grant the permission itself
        // do not grant ettcApi/user/denyPermission
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant($permissionName);
        Account::login($user2);

        // remove dummy permission for user1
        $_POST['userId'] = $user1->getId();
        $_POST['permission'] = $permissionName;

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoPermissions', $error);
    }

    public function testPermissionCanNotBeRemovedIfAdminDoesNotHaveThatPermission()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be removed
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and do not grant the permission itself
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/user/denyPermission");
        Account::login($user2);

        // try to remove dummy permission for user1
        $_POST['userId'] = $user1->getId();
        $_POST['permission'] = $permissionName;

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('PermissionNotGranted', $error);
    }

    public function testNotLoggedIn()
    {
        // create user and do not log in
        $user = $this->createTestUser("phpUnit_Test_User1");

        // try to remove dummy permission for user
        $_POST['userId'] = $user->getId();
        $_POST['permission'] = "phpUnit/dummyPermission1";

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotLoggedIn', $error);
    }

    public function testWrongUserId()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and grant the permission
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/user/denyPermission");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to remove dummy permission for non existant user
        $_POST['userId'] = -1;
        $_POST['permission'] = $permissionName;

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotFound', $error);
    }

    public function testMissingUserId()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/user/denyPermission");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to deny dummy permission without any user
        $_POST['permission'] = $permissionName;

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('MissingValues', $error);
    }

    public function testMissingPermissionName()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be removed
        $user1 = $this->createTestUser("phpUnit_Test_User1");
        $permission = new Permission($this->getDb(), $user1);
        $permission->grant($permissionName);

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/user/denyPermission");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to remove dummy permission without any permission name
        $_POST['userId'] = $user1->getId();

        $denyPermissionApi = new DenyPermission($this->getDb());
        $answer = $denyPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('MissingValues', $error);
    }
}
