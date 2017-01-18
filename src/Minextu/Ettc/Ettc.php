<?php namespace Minextu\Ettc;

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

        $migrator = new Database\Migration\Migrator($currentVersion, $targetVersion, $this->db);
        $status = $migrator->migrateFolder();

        $newCurrentVersion = $migrator->getCurrentVersion();
        if ($currentVersion != $newCurrentVersion) {
            $this->config->set("dbVersion", $newCurrentVersion);
        }

        return $status;
    }

    /**
     * @return   Database\DatabaseInterface   main database
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Get the complete external Url to index.php
     * @return   string   External url to index.php (in form of 'http(s)://server.domain/dir/to/ettc')
     */
    public function getServerUrl()
    {
        if (empty($_SERVER['HTTPS'])) {
            $http = "http://";
        } else {
            $http = "https://";
        }
        $rootDir = dirname($_SERVER['SCRIPT_NAME']);

        $url = $http . $_SERVER['HTTP_HOST'] . $rootDir;
        return $url;
    }
}
