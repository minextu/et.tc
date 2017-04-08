<?php namespace Minextu\Ettc\Account;

use PDO;
use Minextu\Ettc\Database\DatabaseInterface;

/**
 * Use a database to read, modify and add permissions to users and api keys
 */
class PermissionDb
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
     * Get permission csv string for the given userId
     *
     * @param  int $userId Id of the user to get the permissions for
     * @return String          CSV string of all granted permissions for this user
     */
    public function getPermissionsByUserId(int $userId)
    {
        $sql = 'SELECT permissions FROM users WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$userId]);

        $permissions = $stmt->fetchColumn();
        return $permissions;
    }

    /**
     * Get permission csv string for the given rankId
     *
     * @param  int $rankId Id of the rank to get the permissions for
     * @return String          CSV string of all granted permissions for this rank
     */
    public function getPermissionsByRankId(int $rankId)
    {
        $sql = 'SELECT permissions FROM ranks WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$rankId]);

        $permissions = $stmt->fetchColumn();
        return $permissions;
    }

    /**
     * Get permission csv string for the given apiKeyId
     *
     * @param  int $apiKeyId Id of the api key to get the permissions for
     * @return String           CSV string of all granted permissions for this rank
     */
    public function getPermissionsByApiKeyId(int $apiKeyId)
    {
        $sql = 'SELECT permissions FROM userApiKeys WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$apiKeyId]);

        $permissions = $stmt->fetchColumn();
        return $permissions;
    }

    /**
     * Update permissions csv string for the given user
     *
     * @param  int    $userId      Id of the user to update permissions
     * @param  string $permissions CSV of all granted permissions
     * @return bool                  True on success, False otherwise
     */
    public function updatePermissionsForUser(int $userId, string $permissions)
    {
        $sql = 'UPDATE users
                Set permissions = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);

        $status = $stmt->execute([$permissions, $userId]);
        return $status;
    }

    /**
     * Update permissions csv string for the given rank
     *
     * @param  int    $rankId      Id of the rank to update permissions
     * @param  string $permissions CSV of all granted permissions
     * @return bool                  True on success, False otherwise
     */
    public function updatePermissionsForRank(int $rankId, string $permissions)
    {
        $sql = 'UPDATE ranks
                Set permissions = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);

        $status = $stmt->execute([$permissions, $rankId]);
        return $status;
    }

    /**
     * Update permissions csv string for the given apiKey
     *
     * @param  int    $apiKeyId    Id of the api key to update permissions
     * @param  string $permissions CSV of all granted permissions
     * @return bool                  True on success, False otherwise
     */
    public function updatePermissionsForApiKey(int $apiKeyId, string $permissions)
    {
        $sql = 'UPDATE userApiKeys
                Set permissions = ?
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);

        $status = $stmt->execute([$permissions, $apiKeyId]);
        return $status;
    }
}
