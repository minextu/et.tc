<?php namespace Minextu\Ettc\Account;

use PDO;

/**
 * Use a database to read, modify and add api keys
 */
class ApiKeyDb
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
     * Store api key in database
     * @param   int     $userId   Id of user for this api
     * @param   string  $title    Title of the key
     * @param   string  $key      The api key to be saved
     * @return  bool|int          Id of the key on success, False otherwise
     */
    public function addKey($userId, $title, $key)
    {
        $sql = 'INSERT into userApiKeys
                (`userId`, `title`, `key`)
                VALUES (?, ?, ?)';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$userId, $title, $key]);

        if ($status) {
            $status = $this->db->getPdo()->lastInsertId();
        }

        return $status;
    }

    /**
     * Search for api key by id
     * @param    string   $id     Api key id
     * @return   array            Api key info
     */
    public function getApiKeyById($id)
    {
        $sql = 'SELECT `id`,`title`,`key`,`userId`,`created`,`used` FROM userApiKeys WHERE `id`=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $apiKey = $stmt->fetch();
        return $apiKey;
    }

    /**
     * Search for api key by key
     * @param    string   $key    Api key to search for
     * @return   array            Api key info
     */
    public function getApiKeyByKey($key)
    {
        $sql = 'SELECT `id`,`title`,`key`,`userId`,`created`,`used` FROM userApiKeys WHERE `key`=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$key]);

        $apiKey = $stmt->fetch();
        return $apiKey;
    }

    /**
     * Get all api keys by the given user and return the ids
     * @param    int   $userId  User id to get the keys for
     * @return   array          All key ids by the given user
     */
    public function getApiKeyIdsByUserId($userId)
    {
        $sql = "SELECT `id` FROM userApiKeys WHERE userId = ?";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$userId]);

        $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $keys;
    }

    /**
    * Delete an api key from database
    * @param    string   $id            Api key id
    * @return   bool                    True on success, False otherwise
    */
    public function deleteApiKey($id)
    {
        $sql = 'DELETE from userApiKeys
                WHERE id = ?';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$id]);

        return $status;
    }
}
