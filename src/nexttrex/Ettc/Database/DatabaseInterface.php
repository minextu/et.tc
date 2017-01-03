<?php namespace nexttrex\Ettc\Database;

interface DatabaseInterface
{
    /**
     * Create a new instance and connect to the Database
     * @param string $host Database Server
     * @param string $user Database Username
     * @param string $pw   Database Password
     * @param string $db   The Database to be used
     */
    function _construct($host, $user, $pw, $db);
}
