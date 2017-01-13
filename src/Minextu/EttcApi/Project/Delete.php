<?php namespace Minextu\EttcApi\Project;
use Minextu\Ettc\Ettc;

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
 *     @apiErrorExample Error-Response:
 *     HTTP/1.1 403 Forbidden
 *     {
 *       "error": "NoPermissions"
 *     }
 **/

use Respect\Rest\Routable;
use Minextu\Ettc\Project;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Exception\InvalidId;

class Delete implements Routable
{
    public function delete($id)
    {
        $ettc = new Ettc();

        $loggedin = $this->checkLoggedIn($ettc);
        $permissions = $this->checkPermissions($ettc);

        if ($id === false)
        {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        }
        else if (!$loggedin)
        {
            http_response_code(403);
            $answer = ["error" => "NotLoggedIn"];
        }
        else if (!$permissions)
        {
            http_response_code(403);
            $answer = ["error" => "NoPermissions"];
        }
        else
        {
            $invalidId = false;
            try
            {
                $project = new Project($ettc->getDb(), $id);
            }
            catch(InvalidId $e)
            {
                $invalidId = true;
            }

            if ($invalidId)
            {
                http_response_code(404);
                $answer = ["error" => "NotFound"];
            }
            else
            {
                $project->delete();
                $answer = [];
            }
        }

        return $answer;
    }

    private function checkLoggedIn($ettc)
    {
        $loggedin = false;
        $user = Account::checkLogin($ettc->getDb());

        if ($user)
            $loggedin = true;

        return $loggedin;
    }
    private function checkPermissions($ettc)
    {
        $permissions = false;
        $user = Account::checkLogin($ettc->getDb());

        // only proceed if admin
        // TODO: check permissions instead of rank
        if ($user && $user->getRank() == 2)
            $permissions = true;

        return $permissions;
    }
}
