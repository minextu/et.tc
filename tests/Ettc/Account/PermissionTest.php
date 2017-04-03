<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class PermissionTest extends AbstractEttcDatabaseTest
{
    private function createTestUser()
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
    }

    public function testNewUsersDonNotHaveAnyPermissionsByDefault()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user and fetch permissions
        $permission = new Permission($this->getDb(), $user);
        $permissionCount = $permission->count();
        $this->assertEquals(0, $permissionCount, "A newly created user should not have any permissions by default");
    }

    public function testPermissionCanBeAdded()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // add a dummy permission
        $permissionName = "phpUnit/dummyPermission";
        $status = $permission->grant($permissionName);
        $this->assertTrue($status, "grant() did not return True");

        // count permissions
        $permissionCount = $permission->count();
        $this->assertEquals(1, $permissionCount, "There should only one permission");
        // get permission status
        $hasPermission = $permission->get($permissionName);
        $this->assertTrue($hasPermission, "Permission was not saved correctly");

        // recreate permission object, to see if it was saved to database
        $permission = new Permission($this->getDb(), $user);

        // count permissions
        $permissionCount = $permission->count();
        $this->assertEquals(1, $permissionCount, "Permission was not saved to database");
        // get permission status
        $hasPermission = $permission->get($permissionName);
        $this->assertTrue($hasPermission, "Permission was not saved correctly");
    }

    public function testPermissionsCanBeRemoved()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // add two dummy permission
        $permission1Name = "phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // count permissions
        $permissionCount = $permission->count();
        $this->assertEquals(2, $permissionCount, "There should be two permissions");

        // delete the first permissions
        $permission->deny($permission1Name);
        $permissionCount = $permission->count();
        $this->assertEquals(1, $permissionCount, "There should only be one permission left");

        // get permission status
        $hasPermission1 = $permission->get($permission1Name);
        $hasPermission2 = $permission->get($permission2Name);
        $this->assertTrue($hasPermission2, "Wrong permission got deleted");
        $this->assertFalse($hasPermission1, "permission did not get deleted");

        // recreate permission object, to see if it was saved to database
        $permission = new Permission($this->getDb(), $user);

        // count permissions
        $permissionCount = $permission->count();
        $this->assertEquals(1, $permissionCount, "There should only be one permission left");

        // get permission status
        $hasPermission1 = $permission->get($permission1Name);
        $hasPermission2 = $permission->get($permission2Name);
        $this->assertTrue($hasPermission2, "Wrong permission got deleted");
        $this->assertFalse($hasPermission1, "permission did not get deleted");
    }

    public function testAllAvailablePermissionsCanBeReturned()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        $permission = new Permission($this->getDb(), $user);
        $allPermissions = $permission->getAllAvailable();
        $this->assertNotEmpty($allPermissions, "no permissions were returned");
    }

    public function testPermissionAllWillGrantEveryPermissions()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // user should have no permissions
        $hasPermission = $permission->get("phpUnit/dummyPermission3");
        $this->assertFalse($hasPermission, "User already got all permissions without granting!");

        // grant all permissions
        $permission->grant("all");

        // every permission check should return true now
        $hasPermission = $permission->get("phpUnit/dummyPermission3");
        $this->assertTrue($hasPermission, "User has now permissions after adding the 'all' permission");
    }

    public function testAllPermissionsCanBeReturned()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // add two dummy permission
        $permission1Name = "phpUnit/dummyPermission1";
        $permission->grant($permission1Name);
        $permission2Name = "phpUnit/dummyPermission2";
        $permission->grant($permission2Name);

        // count permissions
        $permissionCount = $permission->count();
        $this->assertEquals(2, $permissionCount, "There should be two permissions");

        $this->assertEquals([$permission1Name, $permission2Name], $permission->getAll());
    }

    public function testPermissionOnlyWorkWithSavedUser()
    {
        // create a new user, but do not save it
        $user = new User($this->getDb());
        $nickname = "phpUnit_Test_User";
        $email = "testuser@nexttrex.de";
        $password = "abc123";
        $user->setNick($nickname);
        $user->setEmail($email);
        $user->setPassword($password);


        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $permission = new Permission($this->getDb(), $user);
    }

    public function testPermissionNameMustNotContainComma()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // try to add a permission that contains a comma
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidName');
        $permission->grant("phpUnit/test,phpUnit/shouldNotWork");
    }
    public function testPermissionNameMustNotBeEmpty()
    {
        // create and load user
        $this->createTestUser();
        $user = new User($this->getDb(), 1);

        // create permission object for this user
        $permission = new Permission($this->getDb(), $user);

        // try to add a permission that contains a comma
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidName');
        $permission->grant("");
    }
}
