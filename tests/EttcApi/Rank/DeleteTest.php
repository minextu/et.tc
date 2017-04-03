<?php namespace Minextu\EttcApi\Rank;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Account\Rank;

class DeleteTest extends AbstractEttcDatabaseTest
{
    public function init()
    {
        // delete predefined ranks, since these interfere with testing
        $sql = "truncate ranks";
        $this->getDb()->getPdo()->prepare($sql)->execute();
    }

    private function createLoginTestUser($grantPermission=true)
    {
        $user = new User($this->getDb());
        $user->setNick("phpUnit_test");
        $user->setPassword("test123");
        $user->create();

        // add rank create permission
        if ($grantPermission) {
            $permission = new Permission($this->getDb(), $user);
            $permission->grant("ettcApi/rank/delete");
        }

        Account::login($user, $this->getDb());
    }

    public function createTestRank($title)
    {
        $rank = new Rank($this->getDb());
        $rank->setTitle($title);
        $rank->create();
    }

    public function testRankCanBeDeleted()
    {
        $this->createLoginTestUser();

        // create two ranks
        $this->createTestRank("Test1");
        $this->createTestRank("Test2");

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Rank couldn't be deleted (Error: $error)");
        $this->assertEquals(["success" => true], $answer);

        // try to load the second rank
        $apiKey = new Rank($this->getDb(), 2);
        $this->assertEquals("Test2", $apiKey->getTitle());

        // try to load the first rank which was deleted
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Rank($this->getDb(), 1);
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

        // create a rank
        $this->createTestRank("Test Name");

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NotLoggedIn'], $answer);

        // try to load the rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals("Test Name", $rank->getTitle());
    }

    public function testNoPermissions()
    {
        // create a user with no permissions
        $this->createLoginTestUser(false);
        $this->createTestRank("Test1");

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NoPermissions'], $answer);

        // try to load the rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals("Test1", $rank->getTitle());
    }

    public function testWrongRankId()
    {
        $this->createLoginTestUser();
        $this->createTestRank("Test1");

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(2);

        $this->assertEquals(['error' => 'NotFound'], $answer);

        // try to load the rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals("Test1", $rank->getTitle());
    }
}
