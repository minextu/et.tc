<?php namespace nexttrex\Ettc;
use \PDO;
use nexttrex\Ettc\Database\Migration\Migrator;

abstract class AbstractEttcDatabaseTest extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null)
        {
            if (self::$pdo == null)
            {
                // load test database config
                $config = new Config();
                $config->load();
                $host = $config->get("testDbHost");
                $user = $config->get("testDbUser");
                $pw = $config->get("testDbPassword");
                $db = $config->get("testDbDatabase");
                $charset = 'utf8';

                // connect to test Database
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$pdo = new PDO($dsn, $user, $pw, $options);
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, ':mysql:');
        }

        return $this->conn;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
    }

    /**
     * @return \nexttrex\Ettc\Database\Fake
     */
    final public function getDb()
    {
        return new Database\Fake($this->getConnection()->getConnection());
    }

    // migrate test database
    public function setUp()
    {
        // upgrade to newest version
        $currentVersion = 0;
        $targetVersion = true;

        $migrator = new Migrator($currentVersion,$targetVersion,$this->getDb());

        // start migration, this should upgrade 002
        $status = $migrator->migrateFolder();
    }

    // rollback all changes
    public function tearDown()
    {
        // downgrade
        $currentVersion = true;
        $targetVersion = 0;

        $migrator = new Migrator($currentVersion,$targetVersion,$this->getDb());

        // start migration, this should downgrade 002
        $status = $migrator->migrateFolder();
        $this->assertTrue($status);
    }


}
