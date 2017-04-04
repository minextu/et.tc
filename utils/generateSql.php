<?php namespace Minextu\Ettc;

use Minextu\Ettc\Database\Migration\Migrator;

require_once(__DIR__."/../src/autoload.php");

// load database config and connect
$config = new Config();
$config->load();
$host = $config->get("testDbHost");
$user = $config->get("testDbUser");
$pw = $config->get("testDbPassword");
$db = $config->get("testDbDatabase");
$database = new Database\Mysql($host, $user, $pw, $db);

// drop existing tables
dropTables($database);

// upgrade database to latest version
$version = upgrade($database);

// export database to sql
$sql = generateSql($host, $user, $pw, $db);

// save sql to migration file
saveSql($sql, $version);

// remove all tables
function dropTables($database)
{
    $sql = "SHOW TABLES";
    $tables = $datbase->getPdo()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $sql = "DROP TABLE `$table`";
        $database->getPdo()->prepare($sql)->execute();
    }
}

function upgrade($database)
{
    // upgrade to newest version
    $currentVersion = 0;
    $targetVersion = true;

    $migrator = new Migrator($currentVersion, $targetVersion, $database);

    // start migration, this should upgrade all versions
    $migrator->migrateFolder();

    return $migrator->getCurrentVersion();
}

function generateSql($host, $user, $pw, $db)
{
    $cmd = "mysqldump --compact -u $user --password=$pw $db";
    exec($cmd, $sql);
    return $sql;
}

function saveSql($sqlArr, $toVersion)
{
    $filename = __DIR__."/../conf/migrations/000_all.php";

    $sql = "";
    foreach ($sqlArr as $line) {
        $sql .= $line;
    }
    $sqlStatements = explode(";", $sql);
    // remove commands
    $sqlStatements = array_filter($sqlStatements,
                        create_function('$line',
                            'return strpos(ltrim($line), "/*") !== 0;'));
    // remove empty entries
    $sqlStatements = array_filter($sqlStatements);

    $content  = "<?php namespace Minextu\Ettc\Database\Migration;\n";
    $content .= "class all extends AbstractMigration\n{\n";

    $content .= generateUpgradeContent($sqlStatements, $toVersion);
    $content .= generateDowngradeContent($sqlStatements, $toVersion);


    $content .= "\n}";


    file_put_contents($filename, $content);
}


function generateUpgradeContent($sqlStatements, $toVersion)
{
    $content  = "\tpublic function upgrade()\n\t{\n\t\t";
    $content .= '$sqlArr = '.var_export($sqlStatements, true).";\n\n\t\t";
    $content .= 'foreach ($sqlArr as $sql) {' . "\n\t\t";
    $content .= '$this->db->getPdo()->prepare($sql)->execute();' . "\n\t\t}\n\t\t";
    $content .= "return $toVersion;";
    $content .= "\n\t}\n\n";

    return $content;
}

function generateDowngradeContent()
{
    $content = "\tpublic function downgrade()\n\t{\n\t\t";
    $content .= 'return false;';
    $content .= "\n\t}";

    return $content;
}
