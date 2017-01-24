<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Update values of a project while checking for permissions
 *
 * @api {post} /project/update/:id update project
 * @apiName updateProject
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {Number} id                  Project id
 * @apiParam {String} [title]                  New project title
 * @apiParam {String} [description]            New project description
 *
 * @apiSuccess {Object} project              Contains info for the updated project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "project" : {
 *           "id": Number,
 *           "title": String,
 *           "description" : String,
 *           "image" : String,
 *           "imageType": "Default|Placeholder",
 *           "gitUrl": String,
 *           "createDate" : Date,
 *           "updateDate" : Date
 *         }
 *     }
 *
 * @apiError MissingValues Id wasn't transmited
 * @apiError NoNewValues   Neither a new title nor a new description were transmited
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No permissions to create a project
 * @apiError NotFound      Project couldn't be found
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *    "error": "NoPermissions"
 * }
 **/

class Update extends AbstractRoutable
{
    /**
     * Updates a exiiting project using post values, checks for permissions
     * @param    int       $id   Project id to be deleted
     * @return   array           api answer, containing the created project on success
     */
    public function post($id=false)
    {
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $description = isset($_POST['description']) ? $_POST['description'] : false;

        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (empty($title) && empty($description)) {
            http_response_code(400);
            $answer = ["error" => "NoNewValues"];
        } elseif (!$loggedin) {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $invalidId = false;
            try {
                $project = new Project($this->getDb(), $id);
            } catch (InvalidId $e) {
                $invalidId = true;
            }

            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                if (!empty($title)) {
                    $project->setTitle($title);
                }
                if (!empty($description)) {
                    $project->setDescription($description);
                }
                $project->update();

                $array = $project->toArray();
                // add url to server to image
                $array['image'] = Ettc\Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];
                $answer = ["project" => $array];
            }
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
