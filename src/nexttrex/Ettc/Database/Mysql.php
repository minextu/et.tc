<?php namespace nexttrex\Ettc\Database;

class Mysql implements DatabaseInterface
{
    private $charset = 'utf8';
    private $connection;

    public function _construct($host, $user, $pw, $db)
    {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->connection = new PDO($dsn, $user, $pw, $options);
    }
}
