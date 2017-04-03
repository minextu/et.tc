<?php namespace Minextu\EttcApi\Rank;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\Rank;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;

class RankListTest extends AbstractEttcDatabaseTest
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
            $permission->grant("ettcApi/ranks");
        }

        Account::login($user, $this->getDb());
    }

    public function createTestRank($title)
    {
        $rank = new Rank($this->getDb());
        $rank->setTitle($title);
        $rank->create();
    }

    public function testApiKeyListCanBeGenerated()
    {
        $this->createLoginTestUser();

        // create two test ranks
        $title = "Test title";
        $this->createTestRank($title);

        $title2 = "Test title2";
        $this->createTestRank($title2);

        $rankApi = new RankList($this->getDb());
        $answer = $rankApi->get();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Rank list couldn't be generated (Error: $error)");

        $items = $answer['items'];
        $this->assertCount(2, $items, "Two ranks were created, so there should be 2 entries in the array");

        $this->assertEquals($title, $items[0]['title']);
        $this->assertEquals($title2, $items[1]['title']);
    }

    public function testEmptyRankList()
    {
        $this->createLoginTestUser();

        $rankApi = new RankList($this->getDb());
        $answer = $rankApi->get();

        $items = $answer['items'];
        $this->assertEmpty($items, "No ranks were saved, so the array should be empty");
    }

    public function testNoPermissions()
    {
        // Create user with no list rank permissions
        $this->createLoginTestUser(false);

        $rankApi = new RankList($this->getDb());

        $answer = $rankApi->get();
        $this->assertEquals('NoPermissions', $answer['error']);
    }
}
