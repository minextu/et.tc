<?php namespace Minextu\Ettc\Project;

class ProjectGitTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        // delete possible old test git repository
        $oldProjectGit = __DIR__."/../../../projects/phpUnitTest";
        if (file_exists($oldProjectGit)) {
            self::delete($oldProjectGit);
        }
    }

    public static function tearDownAfterClass()
    {
        self::setUpBeforeClass();
    }

    private static function delete($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                self::delete(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } elseif (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }

    public function testGitRepositoryCanBeCloned()
    {
        $testGitUrl = "https://github.com/minextu/SiPac.git";

        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertFalse($gitExists, "project shouldn't have a git folder yet");

        $status = $projectGit->gitClone($testGitUrl);
        $this->assertTrue($status, "clone didn't succeed");

        $gitExists = $projectGit->exists();
        $this->assertTrue($gitExists, "project should now have a git folder");

        $this->assertEquals($testGitUrl, $projectGit->getUrl());
    }

    public function textGitRepositoryCanBeLoadedAgain()
    {
        $testGitUrl = "https://github.com/minextu/SiPac.git";

        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertTrue($gitExists, "project should have been created in last test");

        $this->assertEquals($testGitUrl, $projectGit->getUrl());
    }

    public function testExistingRepositoryCanNotBeCloned()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $testGitUrl = "https://github.com/minextu/dummy.git";

        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertTrue($gitExists, "project should already have a git folder created in last test");

        $status = $projectGit->gitClone($testGitUrl);
    }

    public function testInvalidProjectPath()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');

        $projectGit = new ProjectGit("nonExistingPhpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertFalse($gitExists, "project should not exists");

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $url = $projectGit->getUrl();
    }

    public function testCreationDateCanBeExtracted()
    {
        $creationDate = "1374402393";

        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertTrue($gitExists, "project should already have a git folder created in last test");

        $this->assertEquals($creationDate, $projectGit->getCreationDate());
    }

    public function testRepositoryCanBeDeleted()
    {
        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertTrue($gitExists, "project should already have a git folder created in last test");

        $status = $projectGit->delete();
        $this->assertTrue($status, "delete() did not succeed");

        $gitExists = $projectGit->exists();
        $this->assertFalse($gitExists, "project should not have a git folder anymore");

        // try to load repository again
        $projectGit = new ProjectGit("phpUnitTest");
        $gitExists = $projectGit->exists();
        $this->assertFalse($gitExists, "project should not have a git folder anymore");
    }
}
