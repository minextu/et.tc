<?php namespace Minextu\Ettc\Project;

use Minextu\Ettc\Exception\InvalidId;
use Minextu\Ettc\Exception\Exception;

class Project
{
    /**
     * Folder containing images for projects
     * @var   string
     */
    const IMAGE_FOLDER = __DIR__."/../../../assets/images/projects/";

    /**
     * Project Database Interface
     * @var ProjectDb
     */
    private $projectDb;

    /**
     * Project Git Interface
     * @var ProjectGit
     */
    public $git;

    /**
     * Unique project id
     * @var   int
     */
    private $id;

    /**
     * Project title
     * @var   string
     */
    private $title;

    /**
     * Project description
     * @var   string
     */
    private $description;

    /**
     * Project image filename
     * @var   string
     */
    private $image;

    /**
     * Project creation date
     * @var   string
     */
    private $createDate;

    /**
     * Date of the projects last update
     * @var   string
     */
    private $updateDate;

    /**
     * @param   \Minextu\Ettc\Database\DatabaseInterface   $db   Database to be used
     * @param   int   $id                          Project id to be loaded
     */
    public function __construct($db, $id=false)
    {
        $this->projectDb = new ProjectDb($db);

        if ($id !== false) {
            $status = $this->loadId($id);
            if ($status === false) {
                throw new InvalidId("Invalid project id '" . $id . "'");
            }
        }
    }

    /**
     * Get all Projects that are saved in db
     * @param    \Minextu\Ettc\Database\DatabaseInterface   $db   Database to be used
     * @param    string   $sortBy    Sort results by given field
     * @param    string   $order     Order results
     * @return   Project[]                          All found projects
     */
    public static function getAll($db, $sortBy, $order)
    {
        $projectDb = new ProjectDb($db);
        $projectIds = $projectDb->getProjectIds($sortBy, $order);

        $projects = [];
        foreach ($projectIds as $id) {
            $project = new Project($db, $id);
            $projects[] = $project;
        }

        return $projects;
    }

    /**
     * Get project git interface
     * @return   ProjectGit   Project git interface
     */
    public function getGit()
    {
        if (!isset($this->git)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->git;
    }

    /**
     * Get project id
     * @return   string   Project id
     */
    public function getId()
    {
        if (!isset($this->id)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->id;
    }

    /**
     * Sets id and initializes projectGit
     * @param   [type]   $id   [description]
     */
    private function setId($id)
    {
        $this->id = $id;
        $this->git = new ProjectGit($id);
    }

    /**
     * Get project title
     * @return   string   Project title
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->title;
    }

    /**
     * @param   string   $title   Project title
     * @return  bool              True on success, False otherwise
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return true;
    }

    /**
     * Get project description
     * @return   string   Project description
     */
    public function getDescription()
    {
        if (!isset($this->description)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->description;
    }

    /**
     * @param   string   $description   Project description
     * @return  bool                    True on success, False otherwise
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return true;
    }

    /**
     * Get project image
     * @return   string   Project image filename
     */
    public function getImage()
    {
        // return default image if not set
        if (!$this->image) {
            $image = "placeholder.png";
        } else {
            $image = $this->image;
        }

        return $image;
    }

    /**
     * @param   string   $filename      Filename of Image
     * @return  bool                    True on success, False otherwise
     */
    public function setImage($filename)
    {
        $this->image = $filename;
        return true;
    }

    /**
     * Deletes the current Image
     * @return   bool   True on success, False otherwise
     */
    public function deleteImage()
    {
        if ($this->getImageType() == "Default") {
            unlink($this::IMAGE_FOLDER . $this->getImage());
            $this->setImage(false);
        }

        return true;
    }

    /**
     * Get project image type
     * @return   string   "Placeholder" if the image is a placholder, "Default" otherwise
     */
    public function getImageType()
    {
        if (!$this->image) {
            $type = "Placeholder";
        } else {
            $type = "Default";
        }

        return $type;
    }

    /**
     * Get project creation date
     * @return   string   Project creation date
     */
    public function getCreateDate()
    {
        if (!isset($this->createDate)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->createDate;
    }

    /**
     * Set project creation date
     * @param   string   $date   Project creation date (parsable by strtotime)
     * @return  bool                    True on success, False otherwise
     */
    public function setCreateDate($date)
    {
        $date = date("Y-m-d H:i:s", strtotime($date));
        $this->createDate = $date;
        return true;
    }

    /**
     * Get project update date
     * @return   string   Date of the projects last update
     */
    public function getUpdateDate()
    {
        if (!isset($this->updateDate)) {
            throw new Exception("Project has to be loaded first.");
        }

        return $this->updateDate;
    }

    /**
     * Set project creation date
     * @param   string   $date   Project creation date (parsable by strtotime)
     * @return  bool                    True on success, False otherwise
     */
    public function setUpdateDate($date)
    {
        $date = date("Y-m-d H:i:s", strtotime($date));
        $this->updateDate = $date;
        return true;
    }

    /**
     * Get url to projects git repository
     * @return   String|bool   The git url if one was set before, False otherwise
     */
    public function getGitUrl()
    {
        if (!isset($this->git)) {
            throw new Exception("Project has to be loaded first.");
        }

        try {
            $gitUrl = $this->git->getUrl();
        } catch (InvalidId $e) {
            $gitUrl = false;
        }

        return $gitUrl;
    }

    /**
     * Clone the given git repository
     * @param    String   $url   Url to git repository
     */
    public function setGitUrl($url)
    {
        if (!isset($this->git)) {
            throw new Exception("Project has to be loaded first.");
        }

        // delete possible old git repository
        if ($this->git->exists()) {
            $this->git->delete();
        }

        $this->git->clone($url);
    }

    /**
     * Load project info using the id
     * @param    int   $id   Unique project id
     * @return   bool        True if project could be found, False otherwise
    */
    public function loadId($id)
    {
        $project = $this->projectDb->getProjectById($id);
        if ($project === false) {
            return false;
        }

        return $this->load($project);
    }

    /**
     * Assign Values to all private attributes using a project array
     * @param    array   $project   Project array created by a Database Object
     * @return   bool               True on success, False otherwise
    */
    private function load($project)
    {
        $this->setId($project['id']);
        $this->title = $project['title'];
        $this->description = $project['description'];
        $this->image = $project['image'];
        $this->createDate = $project['created'];
        $this->updateDate = $project['updated'];

        return true;
    }

    /**
     * Save Project in Database
     * @return   bool   True on success, False otherwise
     */
    public function create()
    {
        if (isset($this->id)) {
            throw new Exception("Project was loaded and is not allowed to be recreated.");
        }
        if (empty($this->title)) {
            throw new Exception("Title has to set via setTitle first.");
        }
        if (empty($this->description)) {
            throw new Exception("Description has to set via setDescription first.");
        }

        $status = $this->projectDb->insertProject($this->title, $this->description, $this->image, $this->createDate, $this->updateDate);
        if ($status) {
            $this->setId($status);
            $this->createDate = time();
            $this->updateDate = time();

            $status = true;
        }

        return $status;
    }

    /**
     * Update values of an existing project
     * @return   bool   True on success, False otherwise
     */
    public function update()
    {
        if (!isset($this->id)) {
            throw new Exception("Project has to be loaded first.");
        }

        $status = $this->projectDb->updateProject($this->id, $this->title, $this->description, $this->image, $this->createDate, $this->updateDate);

        return $status;
    }

    /**
     * Delete Project from Database
     * @param    bool  $deleteGit  Also delete any possible git repository
     * @return   bool   True on success, False otherwise
     */
    public function delete($deleteGit=true)
    {
        if (!isset($this->id)) {
            throw new Exception("Project has to be loaded first.");
        }

        if ($deleteGit && $this->git->exists()) {
            $this->git->delete();
        }

        $this->deleteImage();
        $status = $this->projectDb->deleteProject($this->id);

        return $status;
    }


    /**
     * Generates array out of all values
     * @return   array   The object as array
     */
    public function toArray()
    {
        $project = [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
            'imageType' => $this->getImageType(),
            'createDate' => $this->getCreateDate(),
            'updateDate' => $this->getUpdateDate(),
            'gitUrl' => $this->getGitUrl(),
        ];

        if ($this->git->exists()) {
            $project['gitCreateTimestamp'] = $this->git->getCreationDate();
            $project['gitUpdateTimestamp']  = $this->git->getUpdateDate();
        }

        return $project;
    }
}
