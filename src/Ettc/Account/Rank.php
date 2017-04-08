<?php namespace Minextu\Ettc\Account;

use Minextu\Ettc\Exception;
use Minextu\Ettc\Database\DatabaseInterface;

/**
  * Can Create, Delete and load ranks from database
  */
class Rank
{
    /**
     * Rank Database Interface
     *
     * @var RankDb
     */
    private $rankDb;

    /**
     * Rank id
     *
     * @var int
     */
    private $id;

    /**
     * Title for this rank
     *
     * @var string
     */
    private $title;

    /**
     * Creates a new Instance. Loads an exising rank if $id is specified
     *
     * @param DatabaseInterface $db Database to be used
     * @param int               $id An existing rank id
     */
    public function __construct(DatabaseInterface $db, $id=false)
    {
        $this->rankDb = new RankDb($db);

        if ($id !== false) {
            $status = $this->loadId($id);
            if ($status === false) {
                throw new Exception\InvalidId("Invalid rank id '" . $id . "'");
            }
        }
    }

    /**
     * Get all ranks that are saved in db
     *
     * @param  DatabaseInterface $db Database to be used
     * @return Ranks[]               All found ranks
     */
    public static function getAll(DatabaseInterface $db)
    {
        $rankDb = new RankDb($db);
        $rankIds = $rankDb->getRankIds();

        $ranks = [];
        foreach ($rankIds as $id) {
            $rank = new Rank($db, $id);
            $ranks[] = $rank;
        }

        return $ranks;
    }

    /**
    * Load rank using an id
     *
    * @param  int $id Rank id
    * @return bool    True if rank could be found, False otherwise
    */
    public function loadId($id)
    {
        $rank = $this->rankDb->getRankById($id);
        if ($rank=== false) {
            return false;
        }

        return $this->load($rank);
    }

    /**
     * Assign Values to all private attributes using an rank array
     *
     * @param  array $rank Rank Array created by a Database Object
     * @return bool              True on success, False otherwise
     */
    private function load(array $rank)
    {
        $this->id = $rank['id'];
        $this->title = $rank['title'];

        return true;
    }

    public function getId()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("No rank loaded or saved yet");
        }
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return true;
    }

    public function getTitle()
    {
        if (!isset($this->title)) {
            throw new Exception\Exception("No rank loaded or saved yet");
        }
        return $this->title;
    }

    /**
     * Saves the rank to database
     *
     * @return bool   True on success, False otherwise
     */
    public function create()
    {
        if (!isset($this->title)) {
            throw new Exception\Exception("setTitle has to be used before saving the rank");
        }
        if (isset($this->id)) {
            throw new Exception\Exception("This rank has already been saved");
        }

        $status = $this->rankDb->addRank($this->title);
        if ($status) {
            $this->id = $status;
            $status = true;
        }

        return $status;
    }

    /**
     * Update values of an existing rank
     *
     * @return bool   True on success, False otherwise
     */
    public function update()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("Rank has to be loaded first.");
        }

        $status = $this->rankDb->updateRank($this->id, $this->title);

        return $status;
    }

    /**
     * Deletes this rank
     *
     * @return bool   True on success, False otherwise
     */
    public function delete()
    {
        if (!isset($this->id)) {
            throw new Exception\Exception("No rank loaded or saved yet");
        }

        $status = $this->rankDb->deleteRank($this->id);
        unset($this->id);

        return $status;
    }

    /**
     * Generates array out of all values
     *
     * @return array   The object as array
     */
    public function toArray()
    {
        $rank = [
            'id' => $this->getId(),
            'title' => $this->getTitle()
        ];

        return $rank;
    }
}
