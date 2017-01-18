<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project;
use Minextu\Ettc\Account\Account;

/**
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
 *           "description" : String
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
            $project = new Project($this->ettc->getDb());
            $project->setTitle($title);
            $project->setDescription($description);
            $project->create();

            $answer = ["project" => $project->toArray()];
        }

        return $answer;
    }

    private function checkLoggedIn()
    {
        $loggedin = false;
        $user = Account::checkLogin($this->ettc->getDb());

        if ($user) {
            $loggedin = true;
        }

        return $loggedin;
    }
    private function checkPermissions()
    {
        $permissions = false;
        $user = Account::checkLogin($this->ettc->getDb());

        // only proceed if admin
        // TODO: check permissions instead of rank
        if ($user && $user->getRank() == 2) {
            $permissions = true;
        }

        return $permissions;
    }
}
