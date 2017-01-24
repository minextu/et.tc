<?php namespace Minextu\EttcUi\Page\Project;

use Minextu\EttcUi\Page\AbstractPagePresenter;

class ProjectPresenter extends AbstractPagePresenter
{
    /**
     * Check if project does exist, and only continue if it does
     * @param   string   $subPage   The subpage to access for this page (project id in this case)
     * @return  bool                True if the subpage is valid, False otherwise
     */
    public function setSubPage($subPage)
    {
        $projectExists = $this->model->checkCorrectProject($subPage);
        return $projectExists;
    }

    /**
     * Set all necessary infomartions on view
     */
    public function init()
    {
        $id = $this->model->getId();
        $image = $this->model->getImage();
        $created = $this->model->getCreateDate();
        $updated = $this->model->getUpdateDate();
        $description = $this->model->getDescription();

        $created = date("d.m.Y H:i", strtotime($created));
        $updated = date("d.m.Y H:i", strtotime($updated));

        $this->view->setId($id);
        $this->view->setImage($image);
        $this->view->setCreateDate($created);
        $this->view->setUpdateDate($updated);
        $this->view->setDescription($description);
    }

    /**
     * Get the title for the current project
     * @return   string   Project title
     */
    public function getProjectTitle()
    {
        return $this->model->getProjectTitle();
    }
}
