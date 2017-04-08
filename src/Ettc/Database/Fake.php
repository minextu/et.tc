<?php namespace Minextu\Ettc\Database;

use Minextu\Ettc\Exception;
use PDO;

/**
 * Dummy Database, used for Testing
 */
class Fake implements DatabaseInterface
{
    /**
     * PDO object
     *
     * @var PDO
     */
    private $pdo;

    public function __construct($pdo, string $user="", string $pw="", string $db="")
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
