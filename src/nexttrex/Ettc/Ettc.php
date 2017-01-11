<?php namespace nexttrex\Ettc;

/**
 * Initializes Config and Database
 */
class Ettc
{
    /**
     * main database
     * @var   Database\DatabaseInterface
     */
    private $db;

    /**
     * main config
     * @var   Config
     */
    private $conf;

    /**
     * Connects to the database by loading the connection settings out of the config file
     */
    public function __construct()
    {
        $this->config = new Config();
        $this->config->load();

        $this->db = new Database\Mysql(
                        $this->config->get("dbHost"),
                        $this->config->get("dbUser"),
                        $this->config->get("dbPassword"),
                        $this->config->get("dbDatabase"));

        // TODO: only migrate on git pull (Migration could break when multible users are accessing the site)
        $this->migrateDb();
    }

    /**
     * Migrates the database to the newest version
     * @return   bool   True on success, False otherwise
     */
    private function migrateDb()
    {
        $currentVersion = $this->config->get("dbVersion");
        $targetVersion = $this->config->get("dbTargetVersion");

        $migrator = new Database\Migration\Migrator($currentVersion,$targetVersion,$this->db);
        $status = $migrator->migrateFolder();

        $newCurrentVersion = $migrator->getCurrentVersion();
        if ($currentVersion != $newCurrentVersion)
            $this->config->set("dbVersion", $newCurrentVersion);

        return $status;
    }
}
