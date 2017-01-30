<?php namespace Minextu\EttcUi\Page\EditProject;

use Minextu\EttcUi\Page\AbstractPagePresenter;
use Minextu\EttcUi;
use Minextu\Ettc;

class EditProjectPresenter extends AbstractPagePresenter
{
    /**
     * Check if the project does exist
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
        $title = $this->model->getProjectTitle();
        $image = $this->model->getImage();
        $created = $this->model->getCreateDate();
        $updated = $this->model->getUpdateDate();
        $createdGit = $this->model->getGitCreateTimestamp();
        $updatedGit = $this->model->getGitUpdateTimestamp();
        $description = $this->model->getDescription();

        $created = date("Y-m-d\TH:i", strtotime($created));
        $updated = date("Y-m-d\TH:i", strtotime($updated));

        if ($createdGit) {
            $createdGit = date("Y-m-d\TH:i", $createdGit);
        }
        if ($updatedGit) {
            $updatedGit = date("Y-m-d\TH:i", $updatedGit);
        }

        $this->view->setId($id);
        $this->view->setTitle($title);
        $this->view->setImage($image);
        $this->view->setCreateDate($created);
        $this->view->setUpdateDate($updated);
        $this->view->setGitCreateDate($createdGit);
        $this->view->setGitUpdateDate($updatedGit);
        $this->view->setDescription($description);

        $checkPermissions = $this->model->checkPermissions();
        if (!$checkPermissions) {
            $this->view->showError("No Permissions!");
        }
    }

    /**
     * Check for permissions to update projects and update the project
     */
    public function updateProjectClicked()
    {
        try {
            $this->model->updateProject();
            $this->view->redirectToProject($this->model->getId());
        } catch (Ettc\Exception\Exception $e) {
            $this->view->showError($e->getMessage());
        } catch (EttcUi\Exception $e) {
            $this->view->showError($e->getMessage());
        }
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
