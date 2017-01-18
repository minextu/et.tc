<?php namespace Minextu\EttcUi\Page\CreateProject;

use \Minextu\EttcUi\Page\AbstractPageModel;
use \Minextu\Ettc\Account\Account;
use \Minextu\EttcApi\Project\Create;
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
     * Add a project to database using the ettc api
     * @param   String   $title         Project Title
     * @param   String   $description   Project Description
     * @return  bool                    True on success, False otherwise
     */
    public function addProject($title, $description)
    {
        $_POST['title'] = $title;
        $_POST['description'] = $description;

        $createApi = new Create();
        $answer = $createApi->post();

        if (isset($answer['error'])) {
            throw new Exception($answer['error']);
        } else {
            return true;
        }
    }
}
