<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Gets git changelog for a project
 *
 * @api {get} /project/changelog/:id get project changelog
 * @apiName getProjectChangelog
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {integer} id    Project id
 *
 * @apiSuccess {Array} changelog              Contains changelog for the project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *      "changelog":  [
 *          {
 *              "authorName": String,
 *              "authorEmail": String,
 *              "authorDateTimestamp": String,
 *              "subject": String
 *           }
 *     ]
 * @apiError MissingValues Id wasn't transmited
 * @apiError NotFound      Projects changelog couldn't be found
 * @apiErrorExample Error-Response:
 * HTTP/1.1 404 Not Found
 * {
 *    "error": "NotFound"
 * }
 *
 **/

class Changelog extends AbstractRoutable
{
    /**
     * Generate changelog array for the given project
     * @return   array   project changelog
     */
    public function get($id=false)
    {
        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } else {
            $changelog = $this->getProjectChangelog($id);

            if (!$changelog) {
                $answer = ["error" => "NotFound"];
            } else {
                $answer = ["changelog" => $changelog];
            }
        }

        return $answer;
    }

    private function getProjectChangelog($id)
    {
        try {
            $project = new Project($this->getDb(), $id);
            $array = $project->getGit()->getLogs();

            return $array;
        } catch (InvalidId $e) {
            return false;
        }
    }
}
