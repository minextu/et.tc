<?php namespace nexttrex\EttcUi\Main;
use nexttrex\EttcUi\AbstractModel;

class MainModel extends AbstractModel
{
    /**
     * main database
     * @var   \nexttrex\Ettc\Database\DatabaseInterface
     */
    private $db;

    /**
     * @return   \nexttrex\Ettc\Database\DatabaseInterface   main database
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param   \nexttrex\Ettc\Database\DatabaseInterface   $db   main database
     */
    public function setDb($db)
    {
        $this->db = $db;
    }
}
