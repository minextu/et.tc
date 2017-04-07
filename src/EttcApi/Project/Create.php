<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project as ProjectObj;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc;

/**
 * Creates a new Project while checking for permissions
 *
 * @api {post} /project/create/ create a new project
 * @apiName createProject
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {String} title                  Projects title
 * @apiParam {String} description            Projects description
 *
 * @apiSuccess {Object} project              Contains info for the newly created project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "project" : {
 *           "id": Number,
 *           "title": String,
 *           "description" : String,
 *           "html" : String,
 *           "image" : String,
 *           "imageType": "Default|Placeholder",
 *           "createDate" : Date,
 *           "updateDate" : Date
 *         }
 *     }
 *
 * @apiError MissingValues Title or description weren't transmited
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No permissions to create a project
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *    "error": "NoPermissions"
 * }
 **/

class Create extends AbstractRoutable
{
    /**
     * Creates a new project using post values, checks for permissions
     * @return   array   api answer, containing the created project on success
     */
    public function post()
    {
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $description = isset($_POST['description']) ? $_POST['description'] : false;

        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if (empty($title) || empty($description)) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $project = new ProjectObj($this->getDb());
            $project->setTitle($title);
            $project->setDescription($description);
            $project->create();

            $array = $project->toArray();
            // add url to server to image
            $array['image'] = Ettc\Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];
            $answer = ["project" => $array];
        }

        return $answer;
    }

    /**
     * Check the current login status
     * @return   bool   True if the user ist logged in, False otherwise
     */
    private function checkLoggedIn()
    {
        $loggedin = false;
        $user = Account::checkLogin($this->getDb());

        if ($user) {
            $loggedin = true;
        }

        return $loggedin;
    }

    /**
     * Check if the current user has permissions to create projects
     * @return   bool   True if the user has permissions, False otherwise
     */
    private function checkPermissions()
    {
        $permissions = false;
        $user = Account::checkLogin($this->getDb());

        // only proceed if admin
        // TODO: check permissions instead of rank
        if ($user && $user->getRank() == 2) {
            $permissions = true;
        }

        return $permissions;
    }
}
