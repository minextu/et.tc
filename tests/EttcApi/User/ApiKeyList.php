<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\ApiKey;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;

class ApiKeyListTest extends AbstractEttcDatabaseTest
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

    public function testApiKeyListCanBeGenerated()
    {
        $this->createLoginTestUser();

        // create two test keys
        $title = "Test title";
        $this->createTestApiKey($title, 1);

        $title2 = "Test title2";
        $this->createTestApiKey($title2, 1);

        $apiKeysApi = new ApiKeyList($this->getDb());
        $answer = $apiKeysApi->get();

        $items = $answer['items'];
        $this->assertCount(2, $items, "Two api keys were created, so there should be 2 entries in the array");

        $this->assertEquals($title, $items[0]['title']);
        $this->assertEquals($title2, $items[1]['title']);
    }

    public function testEmptyApiKeyList()
    {
        $this->createLoginTestUser();

        $apiKeysApi = new ApiKeyList($this->getDb());
        $answer = $apiKeysApi->get();

        $items = $answer['items'];
        $this->assertEmpty($items, "No api keys were saved, so the array should be empty");
    }

    public function testOnlyApiKeysForCurrentUserAreShown()
    {
        $this->createLoginTestUser("phpUnit_test");
        $this->createTestApiKey("User1 Api Key", 1);

        $this->createLoginTestUser("phpUnit_test2");
        $title = "User2 Api Key";
        $this->createTestApiKey($title, 2);

        $apiKeysApi = new ApiKeyList($this->getDb());
        $answer = $apiKeysApi->get();

        $items = $answer['items'];
        $this->assertCount(1, $items, "Only one api keys was created for this user, so there should be 1 entry in the array");
        $this->assertEquals($title, $items[0]['title']);
    }
}
