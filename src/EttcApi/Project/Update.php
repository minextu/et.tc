<?php namespace Minextu\EttcApi\Project;

use Minextu\EttcApi\AbstractRoutable;
use Minextu\Ettc\Project\Project as ProjectObj;
use Minextu\Ettc\Account\Account;
use Minextu\Ettc\Account\Permission;
use Minextu\Ettc;
use Minextu\Ettc\Exception\InvalidId;
use Minextu\EttcApi\Exception\ImageException;

/**
 * Update values of a project while checking for permissions
 *
 * @api        {post} /project/update/:id update project
 * @apiName    updateProject
 * @apiVersion 0.1.0
 * @apiGroup   Project
 *
 * @apiParam {Number} id                       Project id
 * @apiParam {String} [title]                  New project title
 * @apiParam {String} [description]            New project description
 * @apiParam {String} [html]                   New project html code
 * @apiParam {String} [createDate]             New project create date (yyyy-MM-ddThh:mm)
 * @apiParam {String} [updateDate]             New project update date (yyyy-MM-ddThh:mm)
 * @apiParam {File} [image]                    New project image  (does not work in apidoc)
 * @apiParam {bool} [deleteImage=false]        Whether to delete the current image or not
 *
 * @apiSuccess {Object} project              Contains info for the updated project
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "project" : {
 *           "id": Number,
 *           "title": String,
 *           "description" : String,
 *           "html" : String,
 *           "image" : String,
 *           "imageType": "Default|Placeholder",
 *           "gitUrl": String,
 *           "createDate" : Date,
 *           "updateDate" : Date
 *         }
 *     }
 *
 * @apiError MissingValues Id wasn't transmited
 * @apiError NoNewValues   At least one of the optional parameters have to be transmitted
 * @apiError NotLoggedIn   You are not logged in
 * @apiError NoPermissions No permissions to update a project
 * @apiError NotFound      Project couldn't be found
 * @apiError WrongImage    Image is not correct
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 403 Forbidden
 * {
 *    "error": "NoPermissions"
 * }
 **/

class Update extends AbstractRoutable
{
    /**
     * Updates a exiiting project using post values, checks for permissions
     *
     * @param  int $id Project id to be deleted
     * @return array           api answer, containing the created project on success
     */
    public function post($id=false)
    {
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $description = isset($_POST['description']) ? $_POST['description'] : false;
        $html = isset($_POST['html']) ? $_POST['html'] : false;
        $image = isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']) ? $_FILES['image'] : false;
        $createDate = isset($_POST['createDate']) ? $_POST['createDate'] : false;
        $updateDate = isset($_POST['updateDate']) ? $_POST['updateDate'] : false;
        $deleteImage = isset($_POST['deleteImage']) && $_POST['deleteImage'] == "true" ? true : false;

        $loggedin = $this->checkLoggedIn();
        $permissions = $this->checkPermissions();

        if ($id === false) {
            http_response_code(400);
            $answer = ["error" => "MissingValues"];
        } elseif (empty($title) && empty($description) && empty($html) && empty($image) && empty($deleteImage) && empty($createDate) && empty($updateDate)) {
            http_response_code(400);
            $answer = ["error" => "NoNewValues"];
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
                if (!empty($title)) {
                    $project->setTitle($title);
                }
                if (!empty($description)) {
                    $project->setDescription($description);
                }
                if (!empty($html)) {
                    $project->setHtml($html);
                }
                if (!empty($createDate)) {
                    $project->setCreateDate($createDate);
                }
                if (!empty($updateDate)) {
                    $project->setUpdateDate($updateDate);
                }
                if ($deleteImage) {
                    $project->deleteImage();
                }

                if (!empty($image)) {
                    try {
                        $this->uploadImage($project, $image);
                    } catch (ImageException $e) {
                        http_response_code(400);
                        $answer = ["error" => "WrongImage", "errorText" => $e->getMessage()];
                        return $answer;
                    }
                }

                $project->update();

                $array = $project->toArray();
                // add url to server to image
                $array['image'] = Ettc\Ettc::getServerUrl() . "/assets/images/projects/" . $array['image'];
                $answer = ["project" => $array];
            }
        }

        return $answer;
    }

    /**
     * Will check the given image, move it to the correct folder and set the image
     *
     * @param \Minextu\Ettc\Project\Project $project Project for which the image should be saved
     * @param array                         $image   $_FILES image
     */
    private function uploadImage($project, $image)
    {
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            throw new ImageException("Provided file is not an image");
        }
        if ($image["size"] > 500000) {
            throw new ImageException("Image is too big");
        }
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        if ($ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "gif") {
            throw new ImageException("File extension has to be jpg, jpeg, png or gif");
        }

        $filename = $project->getId() . ".$ext";

        // delete possible older image
        $project->deleteImage();

        // move file
        $target = $project::IMAGE_FOLDER . $filename;
        $status = move_uploaded_file($image["tmp_name"], $target);

        if (!$status) {
            throw new ImageException("Image move did not succeed");
        }

        $project->setImage($filename);
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
            $hasPermission = $permissionObj->get("ettcApi/project/update");
        }

        return $hasPermission;
    }
}
