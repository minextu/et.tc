<?php namespace Minextu\EttcApi;

use Minextu\Ettc\Ettc;
use Respect\Rest\Routable;
use Minextu\Ettc\Database\DatabaseInterface;

/**
 * Classes extending this will be able to be used as api call object
 */
abstract class AbstractRoutable implements Routable
{
    /**
     * Main database
     *
     * @var DatabaseInterface
     */
    private $db;

    final public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @return   DatabaseInterface   Database to be used
     */
    final public function getDb()
    {
        return $this->db;
    }
}
