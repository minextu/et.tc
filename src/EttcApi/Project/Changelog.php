<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project as ProjectObj;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Gets git changelog for a project
 *
 * @api        {get} /project/changelog/:id get project changelog
 * @apiName    getProjectChangelog
 * @apiVersion 0.1.0
 * @apiGroup   Project
 *
 * @apiParam {integer} id         Project id
 * @apiParam {integer} [count=10] Number of logs to return
 * @apiParam {integer} [skip=0]   Number of logs to skip
 *
 * @apiSuccess {Array} changelog              Contains changelog for the project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *      "changelog":  [
 *          {
 *              "authorName": String,
 *              "authorEmail": String,
 *              "authorAvatar" : String,
 *              "authorDateTimestamp": String,
 *              "subject": String
 *           },
 *       "count" : Integer
 *     ]
 * @apiError          MissingValues Id wasn't transmited
 * @apiError          NotFound      Projects changelog couldn't be found
 * @apiErrorExample   Error-Response:
 * HTTP/1.1 404 Not Found
 * {
 *    "error": "NotFound"
 * }
 **/

class Changelog extends AbstractRoutable
{
    /**
     * Generate changelog array for the given project
     *
     * @return array   project changelog
     */
    public function get($id=false)
    {
        $count = isset($_GET['count']) && ctype_digit($_GET['count']) ? $_GET['count'] : 10;
        $skip = isset($_GET['skip']) && ctype_digit($_GET['skip']) ? $_GET['skip'] : 0;

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } else {
            $changelog = $this->getProjectChangelog($id, $count, $skip);

            if (!$changelog) {
                $answer = ["error" => "NotFound"];
            } else {
                $answer = $changelog;
            }
        }

        return $answer;
    }

    /**
     * Get git commits as array
     *
     * @param  int $id    id of the project
     * @param  int $count Amount of commits to get
     * @param  int $skip  Amount of commits to skip
     * @return array      Api answer
     */
    private function getProjectChangelog($id, $count, $skip)
    {
        try {
            $project = new ProjectObj($this->getDb(), $id);
            $changelog = $project->getGit()->getLogs($count, $skip);
            $commitsCount = $project->getGit()->getCommitsCount();

            $array = ["changelog" => $changelog, "count" => $commitsCount];
            return $array;
        } catch (InvalidId $e) {
            return false;
        }
    }
}
