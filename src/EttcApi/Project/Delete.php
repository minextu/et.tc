<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project as ProjectObj;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Deletes a Project while checking for permissions
 *
 * @api        {post} /project/delete/:id delete a project
 * @apiName    deleteProject
 * @apiVersion 0.1.0
 * @apiGroup   Project
 *
 * @apiParam {Number} id                  Project id
 *
 * @apiSuccess {bool} success             Status of the deletion
 *
 * @apiError        MissingValues Id wasn't transmited
 * @apiError        NotLoggedIn   You are not logged in
 * @apiError        NoPermissions No permissions to delete this project
 * @apiError        NotFound      Project couldn't be found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *   "error": "NoPermissions"
 * }
 **/

class Delete extends AbstractRoutable
{
    /**
     * Deletes the given project, after checking for permissions
     *
     * @param  int $id Project id to be deleted
     * @return array           Api answers
     */
    public function post($id=false)
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
                $project = new ProjectObj($this->getDb(), $id);
            } catch (InvalidId $e) {
                $invalidId = true;
            }

            if ($invalidId) {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            } else {
                $project->delete();
                $answer = ["success" => true];
            }
        }

        return $answer;
    }

    /**
     * Check the current login status
     *
     * @return bool   True if the user ist logged in, False otherwise
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
     * Check if the current user has permissions
     *
     * @return bool   True if the user has permissions, False otherwise
     */
    private function checkPermissions()
    {
        $hasPermission = false;
        $user = Account::checkLogin($this->getDb());

        if ($user) {
            $permissionObj = new Permission($this->getDb(), $user);
            $hasPermission = $permissionObj->get("ettcApi/project/delete");
        }

        return $hasPermission;
    }
}
