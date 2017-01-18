<?php namespace Minextu\Ettc\Account;

use Hautelook\Phpass\PasswordHash;
use \Minextu\Ettc\Exception;

/**
 * Can Create and Load Info about a User using a Database
 */
class User
{
    /**
     * User Database Interface
     * @var UserDb
     */
    private $userDb;

    /**
     * Unique User Id
     * @var   int
     */
    private $id;

    /**
     * User Nickname
     * @var   string
     */
    private $nick;

    /**
     * User E-Mail
     * @var   string
     */
    private $email;

    /**
     * Hashed User Password
     * @var   string
     */
    private $hash;

    /**
     * User rank id (defaults to guest rank with id 1)
     */
    private $rank = 1;

    /**
     * Creates a new Instance. Loads User Info when $id is specified
     * @param   Database\DatabaseInterface   $db   Database to be used
     * @param   int   $id                          User Id to be loaded
     */
    public function __construct($db, $id=false)
    {
        $this->userDb = new UserDb($db);

        if ($id !== false) {
            $status = $this->loadId($id);
            if ($status === false) {
                throw new Exception\Exception("Invalid User ID '" . $id . "'");
            }
        }
    }

    /**
     * Get the Unique User Id
     * @return   int   Unique User Id
     */
    public function getId()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("User has to be loaded first.");
        }

        return $this->id;
    }

    /**
     * Get User nickname
     * @return   string   User Nickname
     */
    public function getNick()
    {
        if (!isset($this->nick)) {
            throw new Exception\Exception("User has to be loaded first.");
        }

        return $this->nick;
    }

    /**
     * Get User E-Mail
     * @return   string   User E-Mail
     */
    public function getEmail()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("User has to be loaded first.");
        }

        return $this->email;
    }

    /**
     * Get User rank id
     * @return   int   User rank id
     */
    public function getRank()
    {
        if (!isset($this->rank)) {
            throw new Exception\Exception("User has to be loaded first.");
        }

        return $this->rank;
    }

    /**
     * Set User Nickname
     *
     * Throws InvalidNickname on invalid nicknames
     *
     * @param   string   $nick   New User Nickname
     */
    public function setNick($nick)
    {
        if ($this->userDb->getUserByNick($nick) !== false) {
            throw new Exception\InvalidNickname("Nickname does already exist.");
        }
        if (strlen($nick) < 3) {
            throw new Exception\InvalidNickname("Nickname is too short");
        }
        if (strlen($nick) > 30) {
            throw new Exception\InvalidNickname("Nickname is too long");
        }

        $this->nick = $nick;

        return true;
    }

    /**
     * Set User E-Mail
     * @param   string   $email   New User E-Mail
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return true;
    }

    /**
     * Set User Password
     *
     * Throws InvalidPasswordException on invalid Passwords
     *
     * @param   string   $password   New User Password
     */
    public function setPassword($password)
    {
        if (strlen($password) < 6) {
            throw new Exception\InvalidPassword("Password is too short");
        }

        $this->hash = $this->hashPassword($password);
        return true;
    }

    /**
     * Set user rank id
     *
     * @param   int   $rank   User rank id
     */
    public function setRank($rank)
    {
        // TODO: check if rank exists

        $this->rank = $rank;
        return true;
    }

    /**
     * Load User Info using a nickname
     * @param    string   $nick   User Nickname to search for
     * @return   bool             True if User could be found, False otherwise
     */
    public function loadNick($nick)
    {
        $user = $this->userDb->getUserByNick($nick);
        if ($user === false) {
            return false;
        }

        return $this->load($user);
    }

    /**
     * Load User Info using the unique Id
     * @param    int   $id   Unique User Id
     * @return   bool        True if User could be found, False otherwise
     */
    public function loadId($id)
    {
        $user = $this->userDb->getUserById($id);
        if ($user === false) {
            return false;
        }

        return $this->load($user);
    }

    /**
     * Assign Values to all private attributes using a user array
     * @param    array   $user   User Array created by a Database Object
     * @return   bool            True on success, False otherwise
     */
    private function load($user)
    {
        $this->id = $user['id'];
        $this->nick = $user['nick'];
        $this->email = $user['email'];
        $this->hash = $user['hash'];
        $this->rank = $user['rank'];

        return true;
    }

    /**
     * Check if the given Password is correct for this User
     * @param    string   $password   Password to be checked
     * @return   bool                 True if the Password is correct, False otherwise
     */
    public function checkPassword($password)
    {
        if (!isset($this->hash)) {
            throw new Exception\Exception("User has to be loaded first.");
        }

        $hasher = new PasswordHash(8, false);
        $check = $hasher->CheckPassword($password, $this->hash);

        return $check;
    }

    /**
     * Save User in Database
     * @return   bool   True on success, False otherwise
     */
    public function create()
    {
        if (isset($this->id)) {
            throw new Exception\Exception("User was loaded and is not allowed to be recreated.");
        }
        if (empty($this->nick)) {
            throw new Exception\Exception("Nickname has to set via setNick first.");
        }
        if (empty($this->hash)) {
            throw new Exception\Exception("Password has to set via setPassword first.");
        }

        $status = $this->userDb->insertUser($this->nick, $this->email, $this->hash, $this->rank);
        if ($status) {
            $this->id = $status;
            $status = true;
        }
        return $status;
    }

    /**
     * Hash Password string using Hautelook\Phpass
     * @param    string   $password   Password to be hashed
     * @return   string               Hashed Password
     */
    private function hashPassword($password)
    {
        $hasher = new PasswordHash(8, false);
        $hash = $hasher->HashPassword($password);
        if (strlen($hash) >= 20) {
            return $hash;
        } else {
            throw new Exception\Exception("Invalid Hash");
        }
    }
}
