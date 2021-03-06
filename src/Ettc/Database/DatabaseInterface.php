<?php namespace Minextu\Ettc\Database;

/**
 * Used to interface with a Database using PDO
 */
interface DatabaseInterface
{
    /**
     * Create a new instance and connect to the Database
     *
     * @param string $host Database Server
     * @param string $user Database Username
     * @param string $pw   Database Password
     * @param string $db   The Database to be used
     */
    public function __construct($host, $user, $pw, $db);

    /**
     * Returns the PDO Object for the Database
     *
     * @return \PDO   PDO Object for the Database
     */
    public function getPdo();
}
