<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Project\Project;

class UpdateTest extends AbstractEttcDatabaseTest
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
            $permission->grant("ettcApi/project/update");
        }

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
        $this->createLoginTestUser();
        $this->createTestProject("oldTestTitle", "oldTestDescription");

        $newTitle = "New Test Title";
        $newDescription = "New Test Description";
        $newHtml = "<p>new html</p>";
        $newCreateDate = "2017-01-20T21:34:00";
        $newUpdateDate = "2016-02-22T20:10:00";

        $_POST['title'] = $newTitle;
        $_POST['description'] = $newDescription;
        $_POST['html'] = $newHtml;
        $_POST['createDate'] = $newCreateDate;
        $_POST['updateDate'] = $newUpdateDate;

        $updateApi = new Update($this->getDb());

        $answer = $updateApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Project couldn't be updated (Error: $error)");
        $this->assertArrayHasKey("project", $answer, "Created project wasn't returned");

        // convert update/creation date to mysql time
        $newCreateDate = str_replace("T", " ", $newCreateDate);
        $newUpdateDate = str_replace("T", " ", $newUpdateDate);

        // check if project values have changed
        $project = $answer['project'];
        $this->assertEquals($newTitle, $project['title']);
        $this->assertEquals($newDescription, $project['description']);
        $this->assertEquals($newHtml, $project['html']);
        $this->assertEquals($newCreateDate, $project['createDate']);
        $this->assertEquals($newUpdateDate, $project['updateDate']);

        // check if project can be loaded using an project object
        $project = new Project($this->getDb(), 1);
        $this->assertEquals($newTitle, $project->getTitle(), "title didn't get updated after reloading");
        $this->assertEquals($newDescription, $project->getDescription(), "description didn't get updated after reloading");
        $this->assertEquals($newHtml, $project->getHtml(), "html didn't get updated after reloading");
        $this->assertEquals($newCreateDate, $project->getCreateDate(), "createDate didn't get updated after reloading");
        $this->assertEquals($newUpdateDate, $project->getUpdateDate(), "updateDate didn't get updated after reloading");
    }

    public function testMissingId()
    {
        $this->createLoginTestUser();

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
        // Create user with no permissions
        $this->createLoginTestUser(false);

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
        $this->createLoginTestUser();

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

        $this->createLoginTestUser();

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
