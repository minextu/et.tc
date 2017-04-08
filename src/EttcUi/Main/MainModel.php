<?php namespace Minextu\EttcUi\Main;

use Minextu\EttcUi\AbstractModel;

class MainModel extends AbstractModel
{
    /**
     * main database
     *
     * @var \Minextu\Ettc\Database\DatabaseInterface
     */
    private $db;

    /**
     * @return   \Minextu\Ettc\Database\DatabaseInterface   main database
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param   \Minextu\Ettc\Database\DatabaseInterface   main database
     */
    public function setDb($db)
    {
        $this->db = $db;
    }
}
