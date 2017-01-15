<?php namespace Minextu\EttcUi;

use Minextu\Ettc\Ettc;

require_once("src/autoload.php");

session_start();

$rootDir = dirname($_SERVER['SCRIPT_NAME']);
$ettc = new Ettc();

$pageName = isset($_GET['page']) ? $_GET['page'] : "Start";
$ettcUi = new EttcUi($ettc, $rootDir, $pageName);

echo $ettcUi->generateHtml();
