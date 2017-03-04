<?php namespace Minextu\Ettc\Project;

use PDO;

/**
 * Use a database to read, modify and add projects
 */
class ProjectDb
{
    /**
     * Main database
     * @var   \Minextu\Ettc\Database\DatabaseInterface
     */
    private $db;

    /**
     * @param   \Minextu\Ettc\Database\DatabaseInterface   $db   Main database
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Search for a project by id
     * @param    int   $id   Unique project id to be searched for
     * @return   array       Project info
     */
    public function getProjectById($id)
    {
        $sql = 'SELECT id, title, description, html, image, created, updated
                FROM projects WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $project = $stmt->fetch();
        return $project;
    }

    /**
     * Get all Projects and return the ids
     * @param    string   $sortBy    Sort results by given field
     * @param    string   $order     Order results
     * @return   array       All Project ids
     */
    public function getProjectIds($sortBy, $order)
    {
        $allowedSort  = ["title","created","updated"];
        $key     = array_search($sortBy, $allowedSort);
        $sortBy = $allowedSort[$key];

        $allowedOrder  = ["asc","desc"];
        $key     = array_search($order, $allowedOrder);
        $orderBy = $allowedOrder[$key];

        $sql = "SELECT id FROM projects ORDER BY $sortBy $order";

        $ids = $this->db->getPdo()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        return $ids;
    }

    /**
    * Store project in database
    * @param    string   $title         Project title
    * @param    string   $description   Project description
    * @param    string   $html          Project html code
    * @param    string   $image         Filename of image for project
    * @param    string   $createDate    Project creation date
    * @param    string   $updateDate    Project update date
    * @return   bool|int                Id of the project on success, False otherwise
    */
    public function insertProject($title, $description, $html, $image, $createDate=false, $updateDate=false)
    {
        $sql = 'INSERT into projects
                (title, description, html, image, created, updated)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $description, $html, $image, $createDate, $updateDate]);

        if ($status) {
            $status = $this->db->getPdo()->lastInsertId();
        }

        return $status;
    }

    /**
    * Update values of a project in database
    * @param    string   $id            Project id
    * @param    string   $title         Project title
    * @param    string   $description   Project description
    * @param    string   $html          Project html code
    * @param    string   $image         Filename of image for project
    * @param    string   $createDate    Project creation date
    * @param    string   $updateDate    Project update date
    * @return   bool                    True on success, False otherwise
    */
    public function updateProject($id, $title, $description, $html, $image, $createDate=false, $updateDate=false)
    {
        $sql = 'UPDATE projects
                Set title = ?, description = ?, html = ?, image = ?, created = ?, updated = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $description, $html, $image, $createDate, $updateDate, $id]);

        return $status;
    }

    /**
    * Delete project from database
    * @param    string   $id         Project id
    * @return   bool|int             True on success, False otherwise
    */
    public function deleteProject($id)
    {
        $sql = 'DELETE from projects
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$id]);

        return $status;
    }
}
