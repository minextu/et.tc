<?php namespace Minextu\Ettc\Database;

use Minextu\Ettc\Exception;

/**
 * Dummy Database, used for Testing
 */
class Fake implements DatabaseInterface
{
    /**
     * PDO object
     *
     * @var \PDO
     */
    private $pdo;

    public function __construct($pdo, $user=false, $pw=false, $db=false)
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
