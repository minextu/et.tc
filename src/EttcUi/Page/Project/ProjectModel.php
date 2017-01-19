<?php namespace Minextu\EttcUi\Page\Project;

use \Minextu\EttcUi\Page\AbstractPageModel;
use \Minextu\EttcApi\Project\Project;

class ProjectModel extends AbstractPageModel
{
    private $project;

    /**
     * Check if the project does exist
     * @param    int   $id   Project id
     * @return   bool         True if the project does exist, False otherwise
     */
    public function checkCorrectProject($id)
    {
        return $this->getProject($id);
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

    public function getDescription()
    {
        return $this->project['description'];
    }

    /**
     * Fetch the project using EttcApi
     * @param    int   $id   project id
     * @return   bool        True if the project does exist, False otherwise
     */
    private function getProject($id)
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
