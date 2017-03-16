<?php namespace Minextu\EttcApi\ApiKey;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;

class DeleteTest extends AbstractEttcDatabaseTest
{
    private function createLoginTestUser($nickname="phpUnit_test")
    {
        $user = new User($this->getDb());
        $user->setNick($nickname);
        $user->setPassword("test123");
        $user->create();

        Account::login($user, $this->getDb());
    }

    private function createTestApiKey($title, $userId)
    {
        $user = new User($this->getDb(), $userId);
        $apiKey = new ApiKey($this->getDb());
        $apiKey->setUser($user);
        $apiKey->setTitle($title);
        $apiKey->create();
    }

    public function testApiKeyCanBeDeleted()
    {
        $this->createLoginTestUser();

        // create two api keys
        $this->createTestApiKey("Test1", 1);
        $this->createTestApiKey("Test2", 1);

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Api Key couldn't be deleted (Error: $error)");
        $this->assertEquals(["success" => true], $answer);

        // try to load the second api key
        $apiKey = new ApiKey($this->getDb(), 2);
        $this->assertEquals("Test2", $apiKey->getTitle());

        // try to load the first api key which was deleted
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new ApiKey($this->getDb(), 1);
    }

    public function testMissingId()
    {
        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post();

        $this->assertEquals(['error' => 'MissingValues'], $answer);
    }

    public function testNotLoggedIn()
    {
        // create a test user
        $user = new User($this->getDb());
        $user->setNick("TestNickname");
        $user->setPassword("TestPassword");
        $user->create();

        // create an api key
        $this->createTestApiKey("Test Name", 1);

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NotLoggedIn'], $answer);

        // try to load tzhe api key
        $apiKey = new ApiKey($this->getDb(), 1);
        $this->assertEquals("Test Name", $apiKey->getTitle());
    }

    public function testNoPermissions()
    {
        $this->createLoginTestUser("phpUnit_test1");
        $this->createTestApiKey("Test1", 1);
        $this->createLoginTestUser("phpUnit_test2");
        $this->createTestApiKey("Test2", 2);

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NoPermissions'], $answer);

        // try to load the api key
        $apiKey = new ApiKey($this->getDb(), 1);
        $this->assertEquals("Test1", $apiKey->getTitle());
    }

    public function testWrongApiKeyId()
    {
        $this->createLoginTestUser();
        $this->createTestApiKey("Test1", 1);

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(2);

        $this->assertEquals(['error' => 'NotFound'], $answer);

        // try to load the api key
        $apiKey = new ApiKey($this->getDb(), 1);
        $this->assertEquals("Test1", $apiKey->getTitle());
    }
}
