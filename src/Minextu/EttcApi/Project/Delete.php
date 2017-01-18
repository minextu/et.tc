<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Exception\InvalidId;

/**
 * @api {delete} /project/delete/:id delete a project
 * @apiName deleteProject
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {Number} id                  Project id
 *
 *
 * @apiError MissingValues Id wasn't transmited
 * @apiError NoPermissions No permissions to delete this project
 * @apiError NotFound      Project couldn't be found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *   "error": "NoPermissions"
 * }
 **/

class Delete extends AbstractRoutable
{
    public function delete($id=false)
    {
        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (!$loggedin) {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        } elseif (!$permissions) {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        } else {
            $invalidId = false;
            try {
                $project = new Project($this->ettc->getDb(), $id);
            } catch (InvalidId $e) {
                $invalidId = true;
            }

            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $project->delete();
                $answer = [];
            }
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
