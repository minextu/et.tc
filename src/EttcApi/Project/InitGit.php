<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project as ProjectObj;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc;
use Minextu\Ettc\Exception\InvalidId;
use Minextu\Ettc\Exception\InvalidGitRemote;

/**
 * Init git repository of a project while checking for permissions
 *
 * @api {post} /project/initGit/:id  init git repository
 * @apiName initProjectGit
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {Number} id                       Project id
 * @apiParam {String} gitUrl                   New git url
 *
 * @apiSuccess {Object} project                Contains info for the updated project
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
 * @apiError MissingValues    Id or git url wasn't transmited
 * @apiError InvalidGitRemote The provided git url is invalid
 * @apiError NotLoggedIn      You are not logged in
 * @apiError NoPermissions    No permissions to init git
 * @apiError NotFound         Project couldn't be found
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *    "error": "NoPermissions"
 * }
 **/

class InitGit extends AbstractRoutable
{
    /**
     * Set git url for a project and clones it, checks for permissions
     * @param    int       $id   Project id to be deleted
     * @return   array           api answer, containing the created project on success
     */
    public function post($id=false)
    {
        $gitUrl = isset($_POST['gitUrl']) ? $_POST['gitUrl'] : false;

        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if ($id === false || empty($gitUrl)) {
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
                try {
                    $project->setGitUrl($gitUrl);
                    $array = $project->toArray();
                    // add url to server to image
                    $array['image'] = Ettc\Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];
                    $answer = ["project" => $array];
                } catch (InvalidGitRemote $e) {
                    $answer = ["error" => "InvalidGitRemote", "debugText" => $e->getMessage()];
                }
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
