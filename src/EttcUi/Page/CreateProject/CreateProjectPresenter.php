<?php namespace Minextu\EttcUi\Page\CreateProject;

use Minextu\EttcUi\Page\AbstractPagePresenter;
use Minextu\Ettc;
use Minextu\EttcUi;

class CreateProjectPresenter extends AbstractPagePresenter
{
    public function init()
    {
        $checkPermissions = $this->model->checkPermissions();
        if (!$checkPermissions) {
            $this->view->showError("No Permissions!");
        }
    }

    /**
     * Check for permissions to create projects and create the project
     *
     * @param String $title       Project title
     * @param String $description Project description
     */
    public function addProjectClicked($title, $description)
    {
        try {
            $this->model->addProject($title, $description);
            $this->view->redirectToProjects();
        } catch (Ettc\Exception\Exception $e) {
            $this->view->showError($e->getMessage());
        } catch (EttcUi\Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }
}
