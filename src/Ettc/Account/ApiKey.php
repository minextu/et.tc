<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\Exception;

/**
  * Can Create, Delete and load api keys from database
  */
class ApiKey
{
    /**
     * Api key Database Interface
     * @var ApiKeyDb
     */
    private $apiKeyDb;

    /**
     * Main database
     * @var   \Minextu\Ettc\Database\DatabaseInterface
     */
    private $db;

    /**
     * Unique Api key id
     * @var   int
     */
    private $id;

    /**
     * Optional Title of this api key
     * @var   string
     */
    private $title;

    /**
     * The key itself
     * @var   string
     */
    private $key;

    /**
     * Date of creation
     * @var   string
     */
    private $createDate;

    /**
     * Date of last use for this key
     * @var   string
     */
    private $lastUseDate;

    /**
     * Creates a new Instance. Loads an exising key if $id is specified
     * @param   Database\DatabaseInterface   $db   Database to be used
     * @param   int   $id                     A key id that has been saved already
     */
    public function __construct($db, $id=false)
    {
        $this->db = $db;
        $this->apiKeyDb = new ApiKeyDb($db);

        if ($id !== false) {
            $status = $this->loadId($id);
            if ($status === false) {
                throw new Exception\InvalidId("Invalid project id '" . $id . "'");
            }
        }
    }

    /**
     * Get all api keys for an user that are saved in db
     * @param    \Minextu\Ettc\Database\DatabaseInterface   $db   Database to be used
     * @param    \Minextu\Ettc\Account\User                 $user User for which keys should be fetched
     * @return   ApiKey[]                                         All found keys for this user
     */
    public static function getAll($db, $user)
    {
        $apiKeyDb = new ApiKeyDb($db);
        $keyIds = $apiKeyDb->getApiKeyIdsByUserId($user->getId());

        $keys = [];
        foreach ($keyIds as $id) {
            $key = new ApiKey($db, $id);
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Load Api key using its id
     * @param    int   $id   Unique Api key id
     * @return   bool        True if api key could be found, False otherwise
     */
    public function loadId($id)
    {
        $key = $this->apiKeyDb->getApiKeyById($id);
        if ($key=== false) {
            return false;
        }

        return $this->load($key);
    }

    /**
     * Load Api key using its key
     * @param    int   $key  Unique Api key
     * @return   bool        True if api key could be found, False otherwise
     */
    public function loadKey($key)
    {
        $key = $this->apiKeyDb->getApiKeyByKey($key);
        if ($key=== false) {
            return false;
        }

        return $this->load($key);
    }

    /**
     * Assign Values to all private attributes using an api key array
     * @param    array   $key      Api key Array created by a Database Object
     * @return   bool              True on success, False otherwise
     */
    private function load($key)
    {
        $this->id = $key['id'];
        $this->title = $key['title'];
        $this->user = new User($this->db, $key['userId']);
        $this->key = $key['key'];
        $this->createDate = $key['created'];
        $this->lastUseDate = $key['used'];

        return true;
    }

    /**
     * Get the value of this key
     * @return   string   The key value
     */
    public function get()
    {
        if (!isset($this->key)) {
            throw new Exception\Exception("No key loaded or saved yet");
        }

        return $this->key;
    }

    public function getId()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("No key loaded or saved yet");
        }

        return $this->id;
    }

    public function setUser($user)
    {
        if (isset($this->id)) {
            throw new Exception\Exception("This key has already been saved");
        }

        $userId = $user->getId();
        if ($userId == false) {
            throw new Exception\Exception("User should already be saved");
        }

        $this->user = $user;
        return true;
    }


    public function getUser()
    {
        if (!isset($this->user)) {
            throw new Exception\Exception("No user set");
        }

        return $this->user;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return true;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getLastUseDate()
    {
        return $this->lastUseDate;
    }

    /**
     * Saves the key to database
     * @return   bool   True on success, False otherwise
     */
    public function create()
    {
        if (!isset($this->user)) {
            throw new Exception\Exception("setUser has to be used before saving the a key");
        }
        if (isset($this->id)) {
            throw new Exception\Exception("This key has already been saved");
        }

        $this->generateKey();

        $status = $this->apiKeyDb->addKey($this->user->getId(), $this->title, $this->key);
        if ($status) {
            $this->id = $status;
            $status = true;

            $this->createDate = time();
        }

        return $status;
    }

    /**
     * Generates a secure random key
     * @return   string   The key
     */
    private function generateKey()
    {
        $this->key = bin2hex(random_bytes(10));
    }

    /**
     * Deletes this api key
     * @return   bool   True on success, False otherwise
     */
    public function delete()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("No key loaded or saved yet");
        }

        $status = $this->apiKeyDb->deleteApiKey($this->id);
        unset($this->id);

        return $status;
    }

    /**
     * Generates array out of all values
     * @return   array   The object as array
     */
    public function toArray()
    {
        $apiKey = [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'key' => $this->get(),
            'createDate' => $this->getCreateDate(),
            'lastUseDate' => $this->getLastUseDate()
        ];

        return $apiKey;
    }
}
