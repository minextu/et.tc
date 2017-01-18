<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Project;

class CreateTest extends AbstractEttcDatabaseTest
{
    public function createLoginTestUser($rank)
    {
        $user = new User($this->getDb());
        $user->setNick("TestNickname");
        $user->setPassword("TestPassword");
        $user->setRank($rank);
        $user->create();

        Account::login($user, $this->getDb());
    }

    public function testProjectCanBeCreated()
    {
        $this->createLoginTestUser(2);

        $title = "Test Title";
        $description = "Test Description";
        $_POST['title'] = $title;
        $_POST['description'] = $description;

        $createApi = new Create($this->getDb());

        $answer = $createApi->post();
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Project couldn't be created (Error: $error)");
        $this->assertArrayHasKey("project", $answer, "Created project wasn't returned");

        $project = $answer['project'];
        $this->assertEquals($title, $project['title']);
        $this->assertEquals($description, $project['description']);

        // check if project can be loaded using an project object
        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle());
        $this->assertEquals($description, $project->getDescription());
    }

    public function testMissingTitle()
    {
        $title = "Test Title";
        $description = "Test Description";

        $_POST['description'] = $description;

        $createApi = new Create($this->getDb());
        $answer = $createApi->post();
        $this->assertEquals('MissingValues', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Project($this->getDb(), 1);
    }

    public function testMissingDescription()
    {
        $title = "Test Title";
        $description = "Test Description";

        $_POST['title'] = $title;

        $createApi = new Create($this->getDb());
        $answer = $createApi->post();
        $this->assertEquals('MissingValues', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Project($this->getDb(), 1);
    }

    public function testNotLoggedIn()
    {
        $title = "Test Title";
        $description = "Test Description";
        $_POST['title'] = $title;
        $_POST['description'] = $description;

        $createApi = new Create($this->getDb());
        $answer = $createApi->post();
        $this->assertEquals('NotLoggedIn', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Project($this->getDb(), 1);
    }

    public function testNoPermissions()
    {
        // Create user with guest permissions
        $this->createLoginTestUser(1);

        $title = "Test Title";
        $description = "Test Description";
        $_POST['title'] = $title;
        $_POST['description'] = $description;

        $createApi = new Create($this->getDb());

        $answer = $createApi->post();
        $this->assertEquals('NoPermissions', $answer['error']);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Project($this->getDb(), 1);
    }
}
