<?php namespace Minextu\EttcApi;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project;

/**
 * @api {get} /projects list projects
 * @apiName listProjects
 * @apiVersion 0.1.0
 * @apiGroup Project
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
 *               "imageType": "Default|Placeholder"
 *            }
 *         ]
 *
 **/

class Projects extends AbstractRoutable
{
    public function get()
    {
        $projects = $this->getProjects();

        $answer = ["items" => $projects];
        return $answer;
    }

    private function getProjects()
    {
        $projects = Project::getAll($this->ettc->getDb());

        $projectsArray = [];
        foreach ($projects as $project) {
            $array = $project->toArray();

            // add url to server to image
            $array['image'] = $this->ettc->getServerUrl() . "/assets/images/projects/" . $array['image'];

            $projectsArray[] = $array;
        }

        return $projectsArray;
    }
}
