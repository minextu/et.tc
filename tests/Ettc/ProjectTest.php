<?php namespace Minextu\Ettc;

class ProjectTest extends AbstractEttcDatabaseTest
{
	private function createTestProject()
	{
		$title = "Test Name";
		$description = "A Test Project";

		$project = new Project($this->getDb());

		$titleStatus = $project->setTitle($title);
		$this->assertTrue($titleStatus, "setTitle didn't return True");
		$descriptionStatus = $project->setDescription($description);
		$this->assertTrue($descriptionStatus, "setDescription didn't return True");

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
        $queryTable = $this->getConnection()->createQueryTable('projects', 'SELECT id,title,description FROM projects');
        $expectedTable = $this->createFlatXmlDataSet(__DIR__."/ProjectTest.xml")->getTable("projects");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

	public function testProjectCanBeLoaded()
	{
		// create test project
		$this->createTestProject();

		// get project with id 1
		$project = new Project($this->getDb(), 1);

		$title = $project->getTitle();
		$this->assertEquals("Test Name", $title);
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

}
