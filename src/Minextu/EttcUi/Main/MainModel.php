<?php namespace Minextu\EttcUi\Main;

use Minextu\EttcUi\AbstractModel;

class MainModel extends AbstractModel
{
    /**
     * main ettc object
     * @var   \Minextu\Ettc\Ettc
     */
    private $ettc;

    /**
     * @return   \Minextu\Ettc\Database\DatabaseInterface   main database
     */
    public function getDb()
    {
        return $this->ettc->getDb();
    }

    /**
     * @return   \Minextu\Ettc\Ettc   main ettc object
     */
    public function getEttc()
    {
        return $this->ettc;
    }

    /**
     * @param   \Minextu\Ettc\Ettc   $ettc   main ettc object
     */
    public function setEttc($ettc)
    {
        $this->ettc = $ettc;
    }
}
