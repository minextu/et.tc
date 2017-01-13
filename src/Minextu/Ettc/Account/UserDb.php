<?php namespace Minextu\Ettc\Account;
use PDO;

/**
 * Use a database to read, modify and add users
 */
class UserDb
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
     * Search for a user by id
     * @param    int   $id   Unique User Id to be searched for
     * @return   array       User Info
     */
    public function getUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);

        $user = $stmt->fetch();
        return $user;
    }

    /**
     * Search for user by nickname
     * @param    string   $nick   User nickname to be searched for
     * @return   array            User info
     */
    public function getUserByNick($nick)
    {
        $sql = 'SELECT * FROM users WHERE nick=?';

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$nick]);

        $user = $stmt->fetch();
        return $user;
    }

    /**
     * Store User in Database
     * @param    string   $nick    User nickname
     * @param    string   $email   User e-mail
     * @param    string   $hash    Hashed password
     * @param    int      $rank    User rank id
     * @return   bool              True on success, False otherwise
     */
    public function addUser($nick, $email, $hash, $rank)
    {
        $sql = 'INSERT into users
                (nick, email, hash, rank)
                VALUES (?, ?, ?, ?)';
        $stmt = $this->db->getPdo()->prepare($sql);
        $status = $stmt->execute([$nick, $email, $hash, $rank]);

        return $status;
    }
}
