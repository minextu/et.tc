<?php namespace Minextu\Ettc;

use PDO;

/**
 * Use a database to read, modify and add projects
 */
class ProjectDb
{
    /**
     * Main database
     * @var   Database\DatabaseInterface
     */
    private $db;

    /**
     * @param   Database\DatabaseInterface   $db   Main database
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
        $sql = 'SELECT * FROM projects WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $project = $stmt->fetch();
        return $project;
    }

    /**
     * Get all Projects and return the ids
     * @return   array       All Project ids
     */
    public function getProjectIds()
    {
        $sql = 'SELECT id FROM projects';

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
