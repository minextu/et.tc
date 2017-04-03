<?php namespace Minextu\Ettc\Account;

use PDO;

/**
 * Use a database to read, modify and add ranks
 */
class RankDb
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
     * Stores a rank in database
     * @param   string  $title    Title of the rank
     * @return  bool|int          Id of the rank on success, False otherwise
     */
    public function addRank($title)
    {
        $sql = 'INSERT into ranks
                (`title`)
                VALUES (?)';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title]);

        if ($status) {
            $status = $this->db->getPdo()->lastInsertId();
        }

        return $status;
    }

    /**
     * Search for a rank by id
     * @param    string   $id     Rank id
     * @return   array            Rank info
     */
    public function getRankById($id)
    {
        $sql = 'SELECT `id`,`title` FROM ranks WHERE `id`=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $rank = $stmt->fetch();
        return $rank;
    }

    /**
     * Get all ranks and return the ids
     * @return   array       All rank ids
     */
    public function getRankIds()
    {
        $sql = "SELECT id FROM ranks";

        $ids = $this->db->getPdo()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        return $ids;
    }

    /**
    * Update values of a rank in database
    * @param    string   $id            Rank id
    * @param    string   $title         Rank title
    * @return   bool                    True on success, False otherwise
    */
    public function updateRank($id, $title)
    {
        $sql = 'UPDATE ranks
                Set title = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $id]);

        return $status;
    }

    /**
    * Delete arank from database
    * @param    string   $id            Rank id
    * @return   bool                    True on success, False otherwise
    */
    public function deleteRank($id)
    {
        $sql = 'DELETE from ranks
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$id]);

        return $status;
    }
}
