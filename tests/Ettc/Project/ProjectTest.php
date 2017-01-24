<?php namespace Minextu\Ettc\Project;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class ProjectTest extends AbstractEttcDatabaseTest
{
    private function createTestProject()
    {
        $title = "Test Name";
        $description = "A Test Project";
        $image = "TestImage.png";

        $project = new Project($this->getDb());

        $titleStatus = $project->setTitle($title);
        $this->assertTrue($titleStatus, "setTitle didn't return True");
        $descriptionStatus = $project->setDescription($description);
        $this->assertTrue($descriptionStatus, "setDescription didn't return True");
        $imageStatus = $project->setImage($image);
        $this->assertTrue($imageStatus, "setImage didn't return True");

        $createStatus = $project->create();
        $this->assertTrue($createStatus, "create didn't return True");
    }

    public function testProjectCanBeCreated()
    {
        // create test project
        $this->createTestProject();

        // check if project would be in Database
        $this->assertEquals(1, $this->getConnection()->getRowCount('projects'), "Inserting failed");

        // check if values are saved correctly
        $queryTable = $this->getConnection()->createQueryTable('projects', 'SELECT id,title,description,image FROM projects');
        $expectedTable = $this->createFlatXmlDataSet(__DIR__."/ProjectTest.xml")->getTable("projects");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testProjectCanBeLoaded()
    {
        // create test project
        $this->createTestProject();

        // get project with id 1
        $project = new Project($this->getDb(), 1);

        // test if title is correct
        $title = $project->getTitle();
        $this->assertEquals("Test Name", $title);

        // check if image and image type is correct
        $imageType = $project->getImageType();
        $this->assertEquals("Default", $imageType);

        $image = $project->getImage();
        $this->assertEquals("TestImage.png", $image);
    }

    public function testProjectCanNotBeLoadedByInvalidId()
    {
        $this->createTestProject();

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        // project with id -1 does not exist
        $project = new Project($this->getDb(), -1);
    }

    public function testLoadedProjectCanNotBeCreated()
    {
        $this->createTestProject();

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');

        $project = new Project($this->getDb(), 1);
        $createStatus = $project->create();
    }

    public function testEmptyProjectCanNotBeCreated()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');

        $project = new Project($this->getDb());

        $createStatus = $project->create();
    }

    public function testProjectWithoutTitleCanNotBeCreated()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');

        $project = new Project($this->getDb());
        $project->setDescription("Test");

        $createStatus = $project->create();
    }

    public function testProjectWithoutImageCanBeCreated()
    {
        $project = new Project($this->getDb());
        $project->setTitle("Test");
        $project->setDescription("Test2");

        $createStatus = $project->create();
        $this->assertTrue($createStatus);

        // image should be a placeholder
        $image = $project->getImage();
        $this->assertEquals("placeholder.png", $image);

        $imageType = $project->getImageType();
        $this->assertEquals("Placeholder", $imageType);
    }

    public function testProjectCanBeDeleted()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');

        $this->createTestProject();

        // get project with id 1
        $project = new Project($this->getDb(), 1);

        $status = $project->delete();
        $this->assertTrue($status);

        // try to load the deleted project
        $project = new Project($this->getDb(), 1);
    }

    public function testProjectCanBeConvertedToArray()
    {
        $this->createTestProject();

        // get project with id 1
        $project = new Project($this->getDb(), 1);

        // convert project to array
        $array = $project->toArray();

        // remove create date, update date and git url from array, since these values won't be checked
        unset($array['createDate']);
        unset($array['updateDate']);
        unset($array['gitUrl']);

        $expectedArray = [
            'id' => 1,
            'title' => 'Test Name',
            'description' => 'A Test Project',
            'image' => 'TestImage.png',
            'imageType' => 'Default',
        ];
        $this->assertEquals($expectedArray, $array);
    }

    public function testProjectsCanBeListed()
    {
        $this->createTestProject();

        $projects = Project::getAll($this->getDb(), "title", "asc");
        $this->assertCount(1, $projects, "There should only be one Project");

        $firstProject = $projects[0];
        $this->assertInstanceOf(Project::class, $firstProject);

        $title = $firstProject->getTitle();
        $this->assertEquals("Test Name", $title);
    }

    public function testProjectCanBeUpdated()
    {
        $this->createTestProject();

        $project = new Project($this->getDb(), 1);

        $newTitle = "new title";
        $newDescription = "new description";
        $newImage = "new_image.png";

        $project->setTitle($newTitle);
        $project->setDescription($newDescription);
        $project->setImage($newImage);

        $status = $project->update();
        $this->assertTrue($status, "Project couldn't be updated");

        // try to load project again and check values
        $project = new Project($this->getDb(), 1);
        $this->assertEquals($newTitle, $project->getTitle());
        $this->assertEquals($newDescription, $project->getDescription());
        $this->assertEquals($newImage, $project->getImage());
    }

    public function testNonExistingProjectCanNotBeUpdated()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');

        $project = new Project($this->getDb());

        $newTitle = "new title";
        $newDescription = "new description";
        $newImage = "new_image.png";

        $project->setTitle($newTitle);
        $project->setDescription($newDescription);
        $project->setImage($newImage);

        $status = $project->update();
    }
}
