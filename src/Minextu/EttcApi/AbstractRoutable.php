<?php namespace Minextu\EttcApi;

use Minextu\Ettc\Ettc;
use Respect\Rest\Routable;

/**
 * Classes extending this will be able to be used as api call object
 */
abstract class AbstractRoutable implements Routable
{
    /**
     * Main database
     * @var   Minextu\Ettc\Database\DatabaseInterface
     */
    private $db;
    final public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return   Minextu\Ettc\Database\DatabaseInterface   Database to be used
     */
    final public function getDb()
    {
        return $this->db;
    }
}
