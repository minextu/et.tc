<?php namespace Minextu\EttcUi\Page\CreateProject;

use \Minextu\EttcUi\Page\AbstractPageModel;
use \Minextu\Ettc\Account\Account;
use \Minextu\Ettc\Project;
use \Minextu\EttcUi\Exception;

class CreateProjectModel extends AbstractPageModel
{
    /**
     * Check if the current User has permissions to create projects
     * @return   bool   True if the user has permissions, False otherwise
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
     * Add a project to database
     * @param   String   $title         Project Title
     * @param   String   $description   Project Description
     * @return  bool                    True on success, False otherwise
     */
    public function addProject($title, $description)
    {
        if (empty($title) || empty($description)) {
            throw new Exception("Missing Title or Description");
        }

        $project = new Project($this->mainModel->getDb());
        $project->setTitle($title);
        $project->setDescription($description);
        return $project->create();
    }
}
