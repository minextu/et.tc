<?php namespace Minextu\Ettc;

class Project
{
    /**
     * Project Database Interface
     * @var ProjectDb
     */
    private $projectDb;

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
     * @param   Database\DatabaseInterface   $db   Database to be used
     * @param   int   $id                          Project id to be loaded
     */
    public function __construct($db, $id=false)
    {
        $this->projectDb = new ProjectDb($db);

        if ($id !== false)
        {
            $status = $this->loadId($id);
            if ($status === false)
                throw new Exception\InvalidId("Invalid project id '" . $id . "'");
        }
    }

    /**
     * Get project id
     * @return   string   Project id
     */
    public function getId()
    {
        if (!isset($this->id))
            throw new Exception\Exception("Project has to be loaded first.");

        return $this->id;
    }

    /**
     * Get project title
     * @return   string   Project title
     */
    public function getTitle()
    {
        if (!isset($this->title))
            throw new Exception\Exception("Project has to be loaded first.");

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
        if (!isset($this->description))
            throw new Exception\Exception("Project has to be loaded first.");

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
     * Load project info using the id
     * @param    int   $id   Unique project id
     * @return   bool        True if project could be found, False otherwise
    */
    public function loadId($id)
    {
        $project = $this->projectDb->getProjectById($id);
        if ($project === false)
            return false;

        return $this->load($project);
    }

    /**
     * Assign Values to all private attributes using a project array
     * @param    array   $project   Project array created by a Database Object
     * @return   bool               True on success, False otherwise
    */
    private function load($project)
    {
        $this->id = $project['id'];
        $this->title = $project['title'];
        $this->description = $project['description'];

        return true;
    }

    /**
     * Save Project in Database
     * @return   bool   True on success, False otherwise
     */
    public function create()
    {
        if (isset($this->id))
            throw new Exception\Exception("Project was loaded and is not allowed to be recreated.");
        if (empty($this->title))
            throw new Exception\Exception("Title has to set via setTitle first.");
        if (empty($this->description))
            throw new Exception\Exception("Description has to set via setDescription first.");

        $status = $this->projectDb->addProject($this->title, $this->description);
        if ($status)
        {
            $this->id = $status;
            return true;
        }
        else
            return false;

    }

    /**
     * Delete Project from Database
     * @return   bool   True on success, False otherwise
     */
    public function delete()
    {
        if (!isset($this->id))
            throw new Exception\Exception("Project has to be loaded first.");

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
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription()
        ];
        return $project;
    }
}
