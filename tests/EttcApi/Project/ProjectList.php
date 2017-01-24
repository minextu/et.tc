<?php namespace Minextu\EttcApi\Project;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Project\Project;

class ProjectsTest extends AbstractEttcDatabaseTest
{
    public static function setUpBeforeClass()
    {
        // set http host to be empty (Api will try to use this variable)
        $_SERVER['HTTP_HOST'] = "";
    }

    private function createTestProject($title, $description, $image)
    {
        $project = new Project($this->getDb());
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setImage($image);
        $project->create();
    }

    public function testProjectListCanBeGenerated()
    {
        // create two test projects
        $title = "Test Name";
        $description = "A Test Project";
        $image = "TestImage.png";
        $this->createTestProject($title, $description, $image);

        $title2 = "Test Name2";
        $description2 = "A Test Project2";
        $image2 = "TestImage.png2";
        $this->createTestProject($title2, $description2, $image2);

        $projectsApi = new ProjectList($this->getDb());
        $answer = $projectsApi->get();

        $items = $answer['items'];
        $this->assertCount(2, $items, "Two projects were created, so there should be 2 entries in the array");

        $project = $items[0];
        $this->assertEquals($title, $project['title']);
        $this->assertEquals($description, $project['description']);
        $this->assertEquals("Default", $project['imageType']);
    }

    public function testEmptyProjectList()
    {
        $projectsApi = new ProjectList($this->getDb());
        $answer = $projectsApi->get();

        $items = $answer['items'];
        $this->assertEmpty($items, "No Projects were saved, so the array should be empty");
    }
}
