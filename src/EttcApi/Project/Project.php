<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc;
use Minextu\Ettc\Exception\InvalidId;

/**
 * Gets info for a project
 *
 * @api        {get} /project/info/:id get project info
 * @apiName    getProject
 * @apiVersion 0.1.0
 * @apiGroup   Project
 *
 * @apiParam {integer} id    Project id
 *
 * @apiSuccess {Array} project              Contains info for the project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "project" : {
 *               "id": Number,
 *               "title": String,
 *               "description" : String,
 *               "html" : String,
 *               "image" : String,
 *               "imageType": "Default|Placeholder",
 *               "createDate" : Date,
  *              "updateDate" : Date
 *         }
 * @apiError          MissingValues Id wasn't transmited
 * @apiError          NotFound      Project couldn't be found
 * @apiErrorExample   Error-Response:
 * HTTP/1.1 404 Not Found
 * {
 *    "error": "NotFound"
 * }
 **/

class Project extends AbstractRoutable
{
    /**
     * Generate array for the given project
     *
     * @param  int $id Project id
     * @return array   project info
     */
    public function get($id=false)
    {
        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } else {
            $project = $this->getProject($id);

            if (!$project) {
                $answer = ["error" => "NotFound"];
            } else {
                $answer = ["project" => $project];
            }
        }

        return $answer;
    }

    private function getProject($id)
    {
        try {
            $project = new Ettc\Project\Project($this->getDb(), $id);
            $array = $project->toArray();
            // add url to server to image
            $array['image'] = Ettc\Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];

            return $array;
        } catch (InvalidId $e) {
            return false;
        }
    }
}
