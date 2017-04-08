<?php namespace Minextu\Ettc\Account;

use Minextu\Ettc\Database\DatabaseInterface;
use PDO;

/**
 * Use a database to read, modify and add ranks
 */
class RankDb
{
    /**
     * Main database
     *
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @param   DatabaseInterface $db Main database
     */
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Stores a rank in database
     *
     * @param  string $title Title of the rank
     * @return bool|int          Id of the rank on success, False otherwise
     */
    public function addRank(string $title)
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
     *
     * @param  int $id Rank id
     * @return array   Rank info
     */
    public function getRankById(int $id)
    {
        $sql = 'SELECT `id`,`title` FROM ranks WHERE `id`=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $rank = $stmt->fetch();
        return $rank;
    }

    /**
     * Get all ranks and return the ids
     *
     * @return array       All rank ids
     */
    public function getRankIds()
    {
        $sql = "SELECT id FROM ranks";

        $ids = $this->db->getPdo()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        return $ids;
    }

    /**
    * Update values of a rank in database
     *
    * @param  int    $id    Rank id
    * @param  string $title Rank title
    * @return bool          True on success, False otherwise
    */
    public function updateRank(int $id, string $title)
    {
        $sql = 'UPDATE ranks
                Set title = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$title, $id]);

        return $status;
    }

    /**
    * Delete a rank from database
     *
    * @param  int $id Rank id
    * @return bool    True on success, False otherwise
    */
    public function deleteRank(int $id)
    {
        $sql = 'DELETE from ranks
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$id]);

        return $status;
    }
}
