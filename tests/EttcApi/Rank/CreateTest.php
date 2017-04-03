<?php namespace Minextu\EttcApi\Rank;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Rank;

class CreateTest extends AbstractEttcDatabaseTest
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
            $permission->grant("ettcApi/rank/create");
        }

        Account::login($user, $this->getDb());
    }

    public function testRankCanBeCreated()
    {
        $this->createLoginTestUser();
        $title = "testTitle";

        $createRankApi = new Create($this->getDb());

        $_POST['title'] = $title;
        $answer = $createRankApi->post();

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Rank couldn't be generated (Error: $error)");
        $this->assertArrayHasKey("rank", $answer, "Created rank wasn't returned");

        $rankArr = $answer['rank'];

        // check if rank values are correct
        $this->assertEquals(1, $rankArr['id']);
        $this->assertEquals($title, $rankArr['title']);

        // check if rank can be loaded again
        $rank = new Rank($this->getDb(), 1);

        $this->assertEquals(1, $rank->getId());
        $this->assertEquals($title, $rank->getTitle());
    }

    public function testRankCanNotBeCreatedIfNotLoggedIn()
    {
        $title = "testTitle";

        $createRankApi = new Create($this->getDb());
        $_POST['title'] = $title;
        $answer = $createRankApi->post();

        $this->assertEquals(['error' => "NotLoggedIn"], $answer);
    }

    public function testMissingTitle()
    {
        $title = "Test Title";

        $createRankApi = new Create($this->getDb());
        $answer = $createRankApi->post();
        $this->assertEquals('MissingValues', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $rank = new Rank($this->getDb(), 1);
    }

    public function testNoPermissions()
    {
        // Create user with no create rank permissions
        $this->createLoginTestUser(false);

        $title = "Test Title";
        $_POST['title'] = $title;

        $createRankApi = new Create($this->getDb());

        $answer = $createRankApi->post();
        $this->assertEquals('NoPermissions', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $rank = new Rank($this->getDb(), 1);
    }
}
