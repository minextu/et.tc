<?php namespace Minextu\Ettc\Project;

use Gioffreda\Component\Git\Git;
use Minextu\Ettc\Exception\Exception;
use Minextu\Ettc\Exception\InvalidId;
use Minextu\Ettc\Exception\InvalidGitRemote;

/**
 * Clone and get info for a git repository
 */
class ProjectGit
{
    /**
     * Complete path to project dir
     * @var   string
     */
    private $projectDir;
    /**
     * Main git object
     * @var  \Gioffreda\Component\Git\Git
     */
    private $git;

    /**
     * Set path and init git object
     * @param   string   $id Name for the git repository
     */
    public function __construct($id)
    {
        $this->projectDir = __DIR__."/../../../projects/" . $id . "/";
        $this->git = Git::create($this->projectDir);
    }

    /**
     * Check if the current git repository does exist
     * @return   bool   True if it does exist, False otherwise
     */
    public function exists()
    {
        return Git::isInitialized($this->projectDir);
    }

    /**
     * Clones an existing git repository to $projectPath
     * @param    string   $url   Url to project
     * @return   bool            True on success, False otherwise
     */
    public function clone($url)
    {
        if ($this->exists()) {
            throw new Exception("project git folder '" . $this->projectDir . "' already exists'");
        }

        // create project folder
        $status = mkdir($this->projectDir);
        if ($status == false) {
            throw new Exception("Could not create folder '" . $this->projectDir . "'");
        }

        // clone remote repository
        try {
            $this->git = Git::cloneRemote($url, $this->projectDir);
        } catch (\Gioffreda\Component\Git\Exception\GitProcessException $e) {
            // delete folder again
            rmdir($this->projectDir);
            // rethrow Exception
            throw new InvalidGitRemote($e->getMessage());
        }

        return true;
    }

    /**
     * Get url for the current repository
     * @return   string   The url for this repository
     */
    public function getUrl()
    {
        if (!$this->exists()) {
            throw new InvalidId("project git folder '" . $this->projectDir . "' does not exists'");
        }
        $url = $this->git->remoteGetUrl('origin');
        $url = str_replace("\n", "", $url);

        return $url;
    }

    /**
     * Get all logs for the repository
     * @param    int   $count   Amount of logs to return
     * @param    int   $skip   Amount of logs to skip
     * @return   string   all git logs
     */
    public function getLogs($count, $skip)
    {
        if (!$this->exists()) {
            throw new InvalidId("project git folder '" . $this->projectDir . "' does not exists'");
        }

        return Changelog::generateLogs($this->git, $count, $skip);
    }

    /**
     * Count all commits, that do exist
     * @return   int   Number of commits
     */
    public function getCommitsCount()
    {
        if (!$this->exists()) {
            throw new InvalidId("project git folder '" . $this->projectDir . "' does not exists'");
        }

        $commitsCount = $this->git->run(['rev-list', 'HEAD', "--count"]);
        $commitsCount = str_replace("\n", "", $commitsCount);
        return $commitsCount;
    }

    /**
     * Delete git repository
     */
    public function delete()
    {
        if (!$this->exists()) {
            throw new InvalidId("project git folder '" . $this->projectDir . "' does not exists'");
        }

        $this->deleteFolder($this->projectDir);
    }

    /**
     * Recusivly delete a folder
     * @param    string   $path   The folder that should be deleted
     * @return   bool             False if the folder does not exist, True otherwise
     */
    private function deleteFolder($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                self::deleteFolder(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } elseif (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }
}
