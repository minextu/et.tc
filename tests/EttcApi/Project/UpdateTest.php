<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Project;

class UpdateTest extends AbstractEttcDatabaseTest
{
    public static function setUpBeforeClass()
    {
        // set http host to be empty (Api will try to use this variable)
        $_SERVER['HTTP_HOST'] = "";
    }

    public function createLoginTestUser($rank)
    {
        $user = new User($this->getDb());
        $user->setNick("TestNickname");
        $user->setPassword("TestPassword");
        $user->setRank($rank);
        $user->create();

        Account::login($user, $this->getDb());
    }

    private function createTestProject($title, $description)
    {
        $project = new Project($this->getDb());
        $project->setTitle($title);
        $project->setDescription($description);
        $project->create();
    }

    public function testProjectCanBeUpdate()
    {
        $this->createLoginTestUser(2);
        $this->createTestProject("oldTestTitle", "oldTestDescription");

        $newTitle = "New Test Title";
        $newDescription = "New Test Description";
        $_POST['title'] = $newTitle;
        $_POST['description'] = $newDescription;

        $updateApi = new Update($this->getDb());

        $answer = $updateApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Project couldn't be updated (Error: $error)");
        $this->assertArrayHasKey("project", $answer, "Created project wasn't returned");

        $project = $answer['project'];
        $this->assertEquals($newTitle, $project['title']);
        $this->assertEquals($newDescription, $project['description']);

        // check if project can be loaded using an project object
        $project = new Project($this->getDb(), 1);
        $this->assertEquals($newTitle, $project->getTitle());
        $this->assertEquals($newDescription, $project->getDescription());
    }

    public function testMissingId()
    {
        $this->createLoginTestUser(2);

        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $_POST['title'] = "new title";
        $_POST['description'] = "new description";

        $updateApi = new Update($this->getDb());
        $answer = $updateApi->post();
        $this->assertEquals('MissingValues', $answer['error']);

        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle(), "project title must not be updated");
        $this->assertEquals($description, $project->getDescription(), "project description must not be updated");
    }

    public function testNotLoggedIn()
    {
        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $_POST['title'] = "new title";
        $_POST['description'] = "new description";

        $updateApi = new Update($this->getDb());
        $answer = $updateApi->post(1);
        $this->assertEquals('NotLoggedIn', $answer['error']);

        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle(), "title was updated, despite not being logged in");
        $this->assertEquals($description, $project->getDescription(), "description was updated, despite not being logged in");
    }

    public function testNoPermissions()
    {
        // Create user with guest permissions
        $this->createLoginTestUser(1);

        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $_POST['title'] = "new title";
        $_POST['description'] = "new description";

        $updateApi = new Update($this->getDb());
        $answer = $updateApi->post(1);
        $this->assertEquals('NoPermissions', $answer['error']);

        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle(), "title was updated, despite not having permissions");
        $this->assertEquals($description, $project->getDescription(), "description was updated, despite not having permissions");
    }

    public function testProjectWithNoChangesWillNotGetUpdated()
    {
        $this->createLoginTestUser(2);

        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $updateApi = new Update($this->getDb());

        $answer = $updateApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertEquals('NoNewValues', $answer['error']);

        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle(), "title was updated, despite not giving new values");
        $this->assertEquals($description, $project->getDescription(), "description was updated, despite not giving new values");
    }

    public function testWrongProjectId()
    {
        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $this->createLoginTestUser(2);

        $_POST['title'] = "new title";
        $_POST['description'] = "new description";

        $updateApi = new Update($this->getDb());
        $answer = $updateApi->post(2);

        $this->assertEquals(['error' => 'NotFound'], $answer);

        // try to load project
        $project = new Project($this->getDb(), 1);
        $this->assertEquals($title, $project->getTitle());
    }
}
