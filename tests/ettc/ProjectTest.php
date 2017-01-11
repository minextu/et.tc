<?php namespace nexttrex\Ettc;

class ProjectTest extends AbstractEttcDatabaseTest
{
	public function testProjectCanBeCreated()
	{
        $title = "Test Name";
        $description = "A Test Project";

        $project = new Project();
        $project->setTitle($title);
        $project->setDescription($description);
    }
}
