<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class ApiKeyTest extends AbstractEttcDatabaseTest
{
    private function createTestUser($nickname)
    {
        $user = new User($this->getDb());

        // save user
        $email = "testuser@nexttrex.de";
        $password = "abc123";

        $user->setNick($nickname);
        $user->setEmail($email);
        $user->setPassword($password);

        $user->create();
    }

    private function createTestApiKey()
    {
        $title = "Test key";
        $this->createTestUser("phpUnit_Test_User");
        $user = new User($this->getDb(), 1);

        $apiKey = new ApiKey($this->getDb());

        $setUserStatus = $apiKey->setUser($user);
        $this->assertTrue($setUserStatus, "setUser didn't return True");
        $setTitleStatus = $apiKey->setTitle($title);
        $this->assertTrue($setTitleStatus, "setTitle didn't return True");
        $createStatus = $apiKey->create();
        $this->assertTrue($createStatus, "create didn't return True");

        $key = $apiKey->get();
        return $key;
    }

    public function testApiKeyCanBeCreated()
    {
        $key = $this->createTestApiKey();
        $this->createTestUser("phpUnit_Test_User2");

        $apiKey = new ApiKey($this->getDb(), 1);

        $user = $apiKey->getUser();

        $this->assertEquals("phpUnit_Test_User", $user->getNick(), "api key didn't return the correct user");
        $this->assertEquals($key, $apiKey->get());
        $this->assertEquals("Test key", $apiKey->getTitle());
    }

    public function testApiKeyCanBeLoadedByKey()
    {
        $key = $this->createTestApiKey();
        $this->createTestUser("phpUnit_Test_User2");

        $apiKey = new ApiKey($this->getDb());
        $success = $apiKey->loadKey($key);
        $this->assertTrue($success);

        $user = $apiKey->getUser();

        $this->assertEquals("phpUnit_Test_User", $user->getNick(), "api key didn't return the correct user");
        $this->assertEquals($key, $apiKey->get());
        $this->assertEquals("Test key", $apiKey->getTitle());
    }

    public function testApiKeyCanNotBeLoadedUsingInvalidKey()
    {
        $key = $this->createTestApiKey();

        $apiKey = new ApiKey($this->getDb());
        $success = $apiKey->loadKey("wrong key");
        $this->assertFalse($success);
    }


    public function testApiKeyCanNotBeLoadedUsingInvalidId()
    {
        $key = $this->createTestApiKey();

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        // key with id -1 does not exist
        $apiKey = new ApiKey($this->getDb(), -1);
    }

    public function testKeyWithoutAUserCanNotBeCreated()
    {
        $title = "Test key";
        $this->createTestUser("phpUnit_Test_User");

        $apiKey = new ApiKey($this->getDb());

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $createStatus = $apiKey->create();
    }

    public function testKeyWithoutATitleCanBeCreated()
    {
        $title = "Test key";
        $this->createTestUser("phpUnit_Test_User");
        $user = new User($this->getDb(), 1);

        $apiKey = new ApiKey($this->getDb());
        $apiKey->setUser($user);

        $createStatus = $apiKey->create();
        $this->assertTrue($createStatus);
    }

    public function testApiKeysCanBeListed()
    {
        $key = $this->createTestApiKey();
        $user = new User($this->getDb(), 1);

        $keys = ApiKey::getAll($this->getDb(), $user);
        $this->assertCount(1, $keys, "There should only be one api key");

        $firstKey = $keys[0];
        $this->assertInstanceOf(ApiKey::class, $firstKey);

        $title = $firstKey->getTitle();
        $this->assertEquals("Test key", $title);
    }

    public function testApiKeyCanBeConvertedToArray()
    {
        $key = $this->createTestApiKey();

        // get api key
        $apiKey = new ApiKey($this->getDb(), 1);

        // convert api kex object to array
        $array = $apiKey->toArray();

        // remove create date, since this value won't be checked
        unset($array['createDate']);

        $expectedArray = [
            'id' => 1,
            'title' => 'Test key',
            'key' => $key,
            'lastUseDate' => null
        ];
        $this->assertEquals($expectedArray, $array);
    }

    public function testApiKeyCanBeDeleted()
    {
        $key = $this->createTestApiKey();

        $apiKey = new ApiKey($this->getDb());
        $apiKey->loadKey($key);

        $success = $apiKey->delete();
        $this->assertTrue($success, "delete didn't return True");

        // try to load the deleted key again
        $apiKey = new ApiKey($this->getDb());
        $loaded = $apiKey->loadKey($key);
        $this->assertFalse($loaded, "ApiKey did load again, despite being deleted");
    }

    public function testUnsavedApiKeyCanNotBeDeleted()
    {
        $this->createTestUser("phpUnit_Test_User");
        $apiKey = new ApiKey($this->getDb());
        $user = new User($this->getDb(), 1);

        $apiKey->setUser($user);

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $apiKey->delete();
    }
}
