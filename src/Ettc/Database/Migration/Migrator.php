<?php namespace Minextu\Ettc\Database\Migration;

/**
 * An instance can upgrade a database using objects extending Migration\AbstractMigration
 */
class Migrator
{
    /**
    * Current Database Migration Version
    * @var int
    */
    private $current;

    /**
    * Target Database Migration Version
    * @var int
    */
    private $target;

    /**
    * Database to be migrated
    * @var \Minextu\Ettc\Database\DatabaseInterface
    */
    private $db;

    /**
    * Initializes the Migrator
    *
    * @param  int                                           $currentVersion   Current Database Migration Version
    * @param  int                                           $targetVersion    Target Database Migration Version
    * @param  \Minextu\Ettc\Database\DatabaseInterface   $db               Database to be migrated
    */
    public function __construct($currentVersion, $targetVersion, $db)
    {
        $this->current = $currentVersion;

        if ($targetVersion === true) {
            $targetVersion = 999;
        }

        $this->target = $targetVersion;
        $this->db = $db;
    }

    /**
     * Get the Current Database Migration Version
     * @return int Current Database Migration Version
     */
    public function getCurrentVersion()
    {
        return $this->current;
    }
    /**
    * Migrates the Database using a Folder containing AbstractMigration classes
    *
    * @param  string   $folder   The folder to be used
    * @return bool               True if succeeded, False if not
    */
    public function migrateFolder($folder=__DIR__."/../../../../conf/migrations")
    {
        // handle migrations in correct order
        if ($this->current < $this->target) {
            $files = scandir($folder);
        } elseif ($this->current > $this->target) {
            $files = scandir($folder, 1);
        }
        // do not do anything if target is already reached
        else {
            $files = array();
        }

        foreach ($files as $file) {
            // ignore everything except php files
            if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
                $version = explode("_", $file)[0];

                // include the migration class and create an instance
                (require_once("$folder/$file")) or die("Migration $file not found!");
                $class = str_replace($version."_", "", str_replace(".php", "", $file));
                $class = '\Minextu\Ettc\Database\Migration\\'.$class;
                $migrationObject = new $class();

                // use the int value as version from now on
                $version = intval($version);

                // upgrade
                if ($this->current < $this->target && $version > $this->current) {
                    $status = $this->migrateObject($migrationObject, false);
                    $this->current = $version;
                }
                // downgrade
                elseif ($this->current > $this->target && $version <= $this->current) {
                    $status = $this->migrateObject($migrationObject, true);
                    $this->current = $version-1;
                } else {
                    $status = true;
                }

                if ($status === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
    * Migrates the Database using an Object
    *
    * @param  AbstractMigration   $migrationObject  An object descriping the Migration
    * @param  bool                $downgrade        Executes a Downgrade when true
    * @return bool                                  True if succeeded, False if not
    */
    public function migrateObject($migrationObject, $downgrade=false)
    {
        $migrationObject->setDb($this->db);

        if ($downgrade) {
            $status = $migrationObject->downgrade();
        } else {
            $status = $migrationObject->upgrade();
        }
        //TODO: debug (show migration query)
        return $status;
    }
}
