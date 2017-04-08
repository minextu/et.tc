<?php namespace Minextu\Ettc\Database\Migration;

/**
 * An instance should be able to migrate the Database
 */
abstract class AbstractMigration
{
    /**
     * Database to be migrated
     *
     * @var \Minextu\Ettc\Database\DatabaseInterface
     */
    protected $db;

    /**
     * Sets the DB
     *
     * @param \Minextu\Ettc\Database\DatabaseInterface $db Database to be migrated
     */
    final public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * Upgrade the Database using $this->db
     *
     * @return bool True on success, False otherwise
     */
    abstract public function upgrade();

    /**
     * Dowgrade the Database using $this->db
     *
     * @return bool True on success, False otherwise
     */
    abstract public function downgrade();
}
