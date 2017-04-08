<?php namespace Minextu\EttcUi\Page\EditProject;

use \Minextu\EttcUi\Page\AbstractPageModel;
use \Minextu\EttcApi\Project\Project;
use \Minextu\Ettc\Account\Account;
use \Minextu\EttcApi\Project\Update;
use \Minextu\EttcApi\Project\InitGit;
use \Minextu\EttcUi\Exception;

class EditProjectModel extends AbstractPageModel
{
    private $project;

    /**
     * Check if the project does exist
     *
     * @param  int $id Project id
     * @return bool         True if the project does exist, False otherwise
     */
    public function checkCorrectProject($id)
    {
        return $this->getProject($id);
    }

    /**
     * Check if the current User has permissions to update projects
     *
     * @return bool   True if the user has permissions, False otherwise
     */
    public function checkPermissions()
    {
        $permissions = false;
        $user = Account::checkLogin($this->mainModel->getDb());

        // only proceed if admin
        // TODO: check permissions instead of rank
        if ($user && $user->getRank() == 2) {
            $permissions = true;
        }
        return $permissions;
    }

    /**
     * Update project using the ettc api
     *
     * @return bool                    True on success, False otherwise
     */
    public function updateProject()
    {
        $updateApi = new Update($this->mainModel->getDb());
        $answer = $updateApi->post($this->getId());

        if (isset($answer['error'])) {
            throw new Exception($answer['error']);
        } else {
            return true;
        }
    }

    /**
     * Init git repository using the ettc api
     *
     * @return bool                    True on success, False otherwise
     */
    public function addGitUrl()
    {
        $initGitApi = new InitGit($this->mainModel->getDb());
        $answer = $initGitApi->post($this->getId());

        if (isset($answer['error'])) {
            throw new Exception($answer['error']);
        } else {
            return true;
        }
    }

    public function getId()
    {
        return $this->project['id'];
    }

    public function getImage()
    {
        return $this->project['image'];
    }

    public function getProjectTitle()
    {
        return $this->project['title'];
    }

    public function getCreateDate()
    {
        return $this->project['createDate'];
    }

    public function getUpdateDate()
    {
        return $this->project['updateDate'];
    }

    public function getGitCreateTimestamp()
    {
        if (!empty($this->project['gitCreateTimestamp'])) {
            return $this->project['gitCreateTimestamp'];
        } else {
            return false;
        }
    }

    public function getGitUpdateTimestamp()
    {
        if (!empty($this->project['gitUpdateTimestamp'])) {
            return $this->project['gitUpdateTimestamp'];
        } else {
            return false;
        }
    }

    public function getGitUrl()
    {
        return $this->project['gitUrl'];
    }

    public function getDescription()
    {
        return $this->project['description'];
    }

    /**
    * Fetch the project using EttcApi
     *
    * @param  int $id project id
    * @return bool        True if the project does exist, False otherwise
    */
    public function getProject($id)
    {
        $projectApi = new Project($this->mainModel->getDb());
        $answer = $projectApi->get($id);

        if (isset($answer['error'])) {
            return false;
        } else {
            $this->project = $answer['project'];
            return true;
        }
    }
}
