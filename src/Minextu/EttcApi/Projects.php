<?php namespace Minextu\EttcApi;

use Minextu\Ettc\Ettc;
use Respect\Rest\Routable;
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

class Projects implements Routable
{
    public function get()
    {
        $ettc = new Ettc();

        $projects = $this->getProjects($ettc);

        $answer = ["items" => $projects];
        return $answer;
    }

    private function getProjects($ettc)
    {
        $projects = Project::getAll($ettc->getDb());

        $projectsArray = [];
        foreach ($projects as $project) {
            $array = $project->toArray();

            // add url to server to image
            $array['image'] = $ettc->getServerUrl() . "/assets/images/projects/" . $array['image'];

            $projectsArray[] = $array;
        }

        return $projectsArray;
    }
}
