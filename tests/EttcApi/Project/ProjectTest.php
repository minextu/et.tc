<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc;

class ProjectTest extends AbstractEttcDatabaseTest
{
    public static function setUpBeforeClass()
    {
        // set http host to be empty (Api will try to use this variable)
        $_SERVER['HTTP_HOST'] = "";
    }

    private function createTestProject($title, $description)
    {
        $project = new Ettc\Project\Project($this->getDb());
        $project->setTitle($title);
        $project->setDescription($description);
        $project->create();
    }

    public function testProjectInfoCanBeGet()
    {
        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $projectApi = new Project($this->getDb());
        $answer = $projectApi->get(1);

        $error = isset($answer['error']) ? $answer['error'] : false;
        $this->assertFalse($error, "Project couldn't be get (Error: $error)");
        $this->assertArrayHasKey("project", $answer, "Project wasn't returned");

        $project = $answer['project'];
        $this->assertEquals($title, $project['title']);
        $this->assertEquals($description, $project['description']);
    }

    public function testInvalidProjectId()
    {
        $title = "Test Title";
        $description = "Test Description";
        $this->createTestProject($title, $description);

        $projectApi = new Project($this->getDb());
        $answer = $projectApi->get(-1);
        $this->assertEquals(["error" => "NotFound"], $answer, "An invalid id must not return a project");
    }

    public function testMissingProjectId()
    {
        $projectApi = new Project($this->getDb());
        $answer = $projectApi->get();
        $this->assertEquals(["error" => "MissingValues"], $answer);
    }
}
