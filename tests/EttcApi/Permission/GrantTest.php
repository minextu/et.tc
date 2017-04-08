<?php namespace Minextu\EttcApi\Permission;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;

class GrantTest extends AbstractEttcDatabaseTest
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

    public function testPermissionForUserCanBeGranted()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be added
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        $permission->grant($permissionName);
        Account::login($user2);

        // grant dummy permission for user1
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "user";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permission could not be added (Error: $error)");
        $this->assertEquals(['success' => true], $answer, "Permission wasn't added successfull");

        // check if permission was added
        $permission = new Permission($this->getDb(), $user1);
        $hasPermission = $permission->get($permissionName);
        $this->assertTrue($hasPermission, "Permission wasn't saved");
    }

    public function testNoPermissions()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be added
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant the permission itself
        // do not grant ettcApi/permission/grant:user
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant($permissionName);
        Account::login($user2);

        // grant dummy permission for user1
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "user";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoPermissions', $error);
    }

    public function testPermissionCanNotBeGrantedIfAdminDoesNotHaveThatPermission()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be added
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and do not grant the permission itself
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        Account::login($user2);

        // try to grant dummy permission for user1
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "user";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('PermissionNotGranted', $error);
    }

    public function testNotLoggedIn()
    {
        // create user and do not log in
        $user = $this->createTestUser("phpUnit_Test_User1");

        // try to grant dummy permission for user
        $_POST['entityId'] = $user->getId();
        $_POST['entityType'] = "user";
        $_POST['permission'] = "phpUnit/dummyPermission1";

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotLoggedIn', $error);
    }

    public function testWrongUserId()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant the permission
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to grant dummy permission for non existent user
        $_POST['entityId'] = -1;
        $_POST['entityType'] = "user";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NotFound', $error);
    }

    public function testMissingUserId()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to grant dummy permission without any entity defined
        $_POST['permission'] = $permissionName;
        $_POST['entityType'] = "user";

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('MissingValues', $error);
    }

    public function testWrongEntityType()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to grant dummy permission without any entity defined
        $_POST['permission'] = $permissionName;
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "invalid";

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('InvalidValues', $error);
    }

    public function testMissingPermissionName()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create first user, for which permissions will be added
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant permissions
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("ettcApi/permission/grant:user");
        $permission->grant($permissionName);
        Account::login($user2);

        // try to grant dummy permission without any permission name
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "user";

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('MissingValues', $error);
    }

    public function testCommaInPermissionName()
    {
        $permissionName = "phpUnit/,dummyPermission2";

        // create first user, for which permissions will be added
        $user1 = $this->createTestUser("phpUnit_Test_User1");

        // create + login second user and grant all permissions (it will throw invalidName otherwise)
        $user2 = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user2);
        $permission->grant("all");
        Account::login($user2);

        // try to grant dummy permission containing a comma
        $_POST['entityId'] = $user1->getId();
        $_POST['entityType'] = "user";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('InvalidName', $error);
    }

    public function testPermissionForRankCanBeGranted()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create rank, for which permissions will be added
        $rank = new Rank($this->getDb());
        $rank->setTitle("phpUnit_Test_Rank");
        $rank->create();

        // create + login user and grant permissions
        $user = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/permission/grant:rank");
        $permission->grant($permissionName);
        Account::login($user);

        // grant dummy permission for rank
        $_POST['entityId'] = $rank->getId();
        $_POST['entityType'] = "rank";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permission could not be added (Error: $error)");
        $this->assertEquals(['success' => true], $answer, "Permission wasn't added successfull");

        // check if permission was added
        $permission = new Permission($this->getDb());
        $permission->loadRank($rank);
        $hasPermission = $permission->get($permissionName);
        $this->assertTrue($hasPermission, "Permission wasn't saved");
    }

    public function testPermissionForApiKeyCanBeGranted()
    {
        $permissionName = "phpUnit/dummyPermission2";

        // create api key, for which permissions will be added
        $apiUser = $this->createTestUser("phpUnit_Test_User1");
        $apiKey = new ApiKey($this->getDb());
        $apiKey->setTitle("phpUnit_Test_ApiKey");
        $apiKey->setUser($apiUser);
        $apiKey->create();

        // create + login user and grant permissions
        $user = $this->createTestUser("phpUnit_Test_User2");
        $permission = new Permission($this->getDb(), $user);
        $permission->grant("ettcApi/permission/grant:apiKey");
        $permission->grant($permissionName);
        Account::login($user);

        // grant dummy permission for api key
        $_POST['entityId'] = $apiKey->getId();
        $_POST['entityType'] = "apiKey";
        $_POST['permission'] = $permissionName;

        $grantPermissionApi = new Grant($this->getDb());
        $answer = $grantPermissionApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Permission could not be added (Error: $error)");
        $this->assertEquals(['success' => true], $answer, "Permission wasn't added successfull");

        // check if permission was added
        $permission = new Permission($this->getDb());
        $permission->loadApiKey($apiKey);
        $hasPermission = $permission->get($permissionName);
        $this->assertTrue($hasPermission, "Permission wasn't saved");
    }
}
