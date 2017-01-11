<?php namespace nexttrex\EttcUi;
require_once("src/autoload.php");

session_start();

$rootDir = dirname($_SERVER['SCRIPT_NAME']);
$ettc = new \nexttrex\Ettc\Ettc();

$pageName = isset($_GET['page']) ? $_GET['page'] : "Test";
$ettcUi = new EttcUi($ettc, $rootDir, $pageName);

echo $ettcUi->generateHtml();
