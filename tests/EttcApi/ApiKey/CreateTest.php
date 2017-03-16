<?php namespace Minextu\EttcApi\ApiKey;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\ApiKey;

class CreateTest extends AbstractEttcDatabaseTest
{
    private function createLoginTestUser()
    {
        $user = new User($this->getDb());
        $user->setNick("phpUnit_test");
        $user->setPassword("test123");
        $user->create();

        Account::login($user, $this->getDb());
    }

    public function testApiKeyCanBeCreated()
    {
        $this->createLoginTestUser();
        $title = "testTitle";

        $addApiKeyApi = new Create($this->getDb());

        $_POST['title'] = $title;
        $answer = $addApiKeyApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "ApiKey couldn't be generated (Error: $error)");
        $this->assertArrayHasKey("key", $answer, "Created key wasn't returned");

        $apiKeyArr = $answer['key'];

        // check if key values are correct
        $this->assertEquals(1, $apiKeyArr['id']);
        $this->assertEquals($title, $apiKeyArr['title']);

        // check if api key can be loaded using ApiKey
        $apiKey = new ApiKey($this->getDb());
        $loaded = $apiKey->loadKey($apiKeyArr['key']);
        $this->assertTrue($loaded);

        $this->assertEquals(1, $apiKey->getId());
        $this->assertEquals($title, $apiKey->getTitle());
        $this->assertEquals($apiKeyArr['key'], $apiKey->get());
    }

    public function testApiKeyCanNotBeCreatedIfNotLoggedIn()
    {
        $title = "testTitle";

        $addApiKeyApi = new Create($this->getDb());
        $_POST['title'] = $title;
        $answer = $addApiKeyApi->post();

        $this->assertEquals(['error' => "NotLoggedIn"], $answer);
    }
}
