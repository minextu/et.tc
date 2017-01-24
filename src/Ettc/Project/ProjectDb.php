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
        $sql = 'SELECT id, title, description, image, created, updated
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
    * @return   bool|int                Id of the project on success, False otherwise
    */
    public function insertProject($title, $description, $image)
    {
        $sql = 'INSERT into projects
                (title, description, image)
                VALUES (?, ?, ?)';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $description, $image]);

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
    * @return   bool                    True on success, False otherwise
    */
    public function updateProject($id, $title, $description, $image)
    {
        $sql = 'UPDATE projects
                Set title = ?, description = ?, image = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $description, $image, $id]);

        return $status;
    }

    /**
    * Delete project from database
    * @param    string   $id         Project id
    * @return   bool|int                Id of the project on success, False otherwise
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
