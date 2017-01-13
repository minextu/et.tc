<?php namespace Minextu\Ettc\Database\Migration;
use Minextu\Ettc\Database;

class MigratorTest extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        if ($this->conn === null)
        {
            if (self::$pdo == null)
                self::$pdo = new \PDO('sqlite::memory:');

            $this->conn = $this->createDefaultDBConnection(self::$pdo, ':memory:');
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
     * @return \Minextu\Ettc\Database\Fake
     */
    public function getDb()
    {
        return new Database\Fake($this->getConnection()->getConnection());
    }

    public function testDatabaseCanBeUpgradedUsingAnObject()
    {
        $migrator = new Migrator(0,0,$this->getDb());

        require_once("testMigrations/001_addASimpleTable.php");
        $migrationObject = new addASimpleTable();

        // upgrade
        $status = $migrator->migrateObject($migrationObject, false);
        $this->assertTrue($status);

        // check if table was created
        $tables = $this->getConnection()->createDataSet();
        $expectedTables = $this->createXMLDataSet(__DIR__.'/testMigrations/001.xml');
        $this->assertDataSetsEqual($expectedTables, $tables);
     }

     public function testDatabaseCanBeDowngradedUsingAnObject()
     {
         $migrator = new Migrator(0,0,$this->getDb());
         require_once("testMigrations/001_addASimpleTable.php");
         $migrationObject = new addASimpleTable();

         // downgrade
         $status = $migrator->migrateObject($migrationObject, true);
         $this->assertTrue($status);

         // check if table was deleted
         $tables = $this->getConnection()->createDataSet();
         $expectedTables = new \PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
         $this->assertDataSetsEqual($expectedTables, $tables);
     }

     public function testDatabaseCanBeUpgradedUsingAFolder()
     {
         // simulate an Upgrade from Version 0 to 3
         $currentVersion = 0;
         $targetVersion = 3;

         $migrator = new Migrator($currentVersion,$targetVersion,$this->getDb());

         // start migration, this should upgrade 001, 002 and 003
         $status = $migrator->migrateFolder(dirname(__FILE__)."/testMigrations");
         $this->assertTrue($status);

         // check if tables were created
         $tables = $this->getConnection()->createDataSet();
         $expectedTables = $this->createXMLDataSet(__DIR__.'/testMigrations/003.xml');
         $this->assertDataSetsEqual($expectedTables, $tables);
     }

     public function testDatabaseCanBeDowngradedUsingAFolder()
     {
         // simulate an Downgrade from Version 3 to 1
         $currentVersion = 3;
         $targetVersion = 1;

         $migrator = new Migrator($currentVersion,$targetVersion,$this->getDb());

         // start migration, this should downgrade 003 and 002
         $status = $migrator->migrateFolder(dirname(__FILE__)."/testMigrations");
         $this->assertTrue($status);

         // check if tables were deleted
         $tables = $this->getConnection()->createDataSet();
         $expectedTables = $this->createXMLDataSet(__DIR__.'/testMigrations/001.xml');
         $this->assertDataSetsEqual($expectedTables, $tables);
     }
}
