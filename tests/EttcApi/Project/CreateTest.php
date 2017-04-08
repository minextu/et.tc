<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Project\Project;

class CreateTest extends AbstractEttcDatabaseTest
{
    public static function setUpBeforeClass()
    {
        // set http host to be empty (Api will try to use this variable)
        $_SERVER['HTTP_HOST'] = "";
    }

    public function createLoginTestUser($grantPermission=true)
    {
        $user = new User($this->getDb());
        $user->setNick("TestNickname");
        $user->setPassword("TestPassword");
        $user->create();

        // set permissions
        if ($grantPermission) {
            $permission = new Permission($this->getDb(), $user);
            $permission->grant("ettcApi/project/create");
        }

        Account::login($user, $this->getDb());
    }

    public function testProjectCanBeCreated()
    {
        $this->createLoginTestUser();

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
        // Create user with no permissions
        $this->createLoginTestUser(false);

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
