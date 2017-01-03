<?php namespace nexttrex\Ettc;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
	public function testProjectCanBeCreated()
	{
        $name = "Test Name";
        $description = "A Test Project";

        $project = new Project();
        $project->setName($name);
        $project->setDescription($description);
    }
}
