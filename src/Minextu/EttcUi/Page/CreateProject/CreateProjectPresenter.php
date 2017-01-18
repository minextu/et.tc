<?php namespace Minextu\EttcUi\Page\CreateProject;

use Minextu\EttcUi\Page\AbstractPagePresenter;
use Minextu\Ettc;
use Minextu\EttcUi;

class CreateProjectPresenter extends AbstractPagePresenter
{
    public function init()
    {
    }

    public function addProjectClicked($title, $description)
    {
        $checkPermissions = $this->model->checkPermissions();
        if (!$checkPermissions) {
            $this->view->showError("No Permissions!");
            return;
        }

        try {
            $this->model->addProject($title, $description);
            $this->view->redirectToProjects();
        } catch (Ettc\Ettc\Exception $e) {
            $this->view->showError($e->getMessage());
        } catch (EttcUi\Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }
}
