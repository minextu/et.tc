<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Project\Project;

class DeleteTest extends AbstractEttcDatabaseTest
{
    public function createLoginTestUser($grantPermission=true)
    {
        $user = new User($this->getDb());
        $user->setNick("TestNickname");
        $user->setPassword("TestPassword");
        $user->create();

        // set permissions
        if ($grantPermission) {
            $permission = new Permission($this->getDb(), $user);
            $permission->grant("ettcApi/project/delete");
        }

        Account::login($user, $this->getDb());
    }

    public function createTestProject()
    {
        $title = "Test Name";
        $description = "A Test Project";

        $project = new Project($this->getDb());
        $project->setTitle($title);
        $project->setDescription($description);
        $project->create();
    }

    //
    // This might also delete possible git repositories outside of this testcase
    //
    /*public function testProjectCanBeDeleted()
    {
        $this->createLoginTestUser();

        // create two projects
        $this->createTestProject();
        $this->createTestProject();

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);
        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Project couldn't be deleted (Error: $error)");
        $this->assertEquals(["success" => true], $answer);

        // try to load the second project
        $project = new Project($this->getDb(), 2);
        $this->assertEquals("Test Name", $project->getTitle());

        // try to load the first project which was deleted
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $project = new Project($this->getDb(), 1);
    }*/

    public function testMissingId()
    {
        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post();

        $this->assertEquals(['error' => 'MissingValues'], $answer);
    }

    public function testNotLoggedIn()
    {
        $this->createTestProject();

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NotLoggedIn'], $answer);

        // try to load project
        $project = new Project($this->getDb(), 1);
        $this->assertEquals("Test Name", $project->getTitle());
    }

    public function testNoPermissions()
    {
        $this->createTestProject();
        // create user with guest rank
        $this->createLoginTestUser(false);

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(1);

        $this->assertEquals(['error' => 'NoPermissions'], $answer);

        // try to load project
        $project = new Project($this->getDb(), 1);
        $this->assertEquals("Test Name", $project->getTitle());
    }

    public function testWrongProjectId()
    {
        $this->createTestProject();
        $this->createLoginTestUser();

        $deleteApi = new Delete($this->getDb());
        $answer = $deleteApi->post(2);

        $this->assertEquals(['error' => 'NotFound'], $answer);

        // try to load project
        $project = new Project($this->getDb(), 1);
        $this->assertEquals("Test Name", $project->getTitle());
    }
}
