<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project;
use Minextu\Ettc\Ettc;

/**
 * Generates a list of projects
 *
 * @api {get} /projects list projects
 * @apiName listProjects
 * @apiVersion 0.1.0
 * @apiGroup Project
 *
 * @apiParam {String=title,created,updated} sortBy=title    Sort result by given field
 * @apiParam {String=asc,desc}  order=asc                   Order result
 *
 * @apiSuccess {Array} items              Contains a list of projects
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "items" : [
 *            {
 *               "id": Number,
 *               "title": String,
 *               "description" : String,
 *               "image" : String,
 *               "imageType": "Default|Placeholder",
 *               "createDate" : Date,
 *               "updateDate" : Date
 *            }
 *         ]
 * @apiError InvalidValues sortBy or orderBy contain invalid values
 * @apiErrorExample Error-Response:
 * HTTP/1.1 400 Bad Request
 * {
 *    "error": "InvalidValues"
 * }
 *
 **/

class ProjectList extends AbstractRoutable
{
    /**
     * Generate a list of Projects
     * @return   array   List of projects
     */
    public function get()
    {
        $sortBy = !empty($_GET['sortBy']) ? $_GET['sortBy'] : "title";
        $order = !empty($_GET['order']) ? $_GET['order'] : "asc";
        $allowedSort = ["title", "created", "updated"];
        $allowedOrder = ["asc", "desc"];

        if (!in_array($sortBy, $allowedSort) || !in_array($order, $allowedOrder)) {
            http_response_code(400);
            $answer = ["error" => "InvalidValues"];
        } else {
            $projects = $this->getProjects($sortBy, $order);
            $answer = ["items" => $projects];
        }

        return $answer;
    }

    /**
     * Get all projects, convert them to arrays
     * @param    string   $sortBy    Sort results by given field
     * @param    string   $order   Order results
     * @return   array   all projects as arrays
     */
    private function getProjects($sortBy, $order)
    {
        $projects = Project::getAll($this->getDb(), $sortBy, $order);

        $projectsArray = [];
        foreach ($projects as $project) {
            $array = $project->toArray();

            // add url to server to image
            $array['image'] = Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];

            $projectsArray[] = $array;
        }

        return $projectsArray;
    }
}
