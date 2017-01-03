<?php namespace nexttrex\EttcUi;
require_once("src/autoload.php");

session_start();

$rootDir = dirname($_SERVER['SCRIPT_NAME']);
$ettcUi = new EttcUi($rootDir, "Test");

echo $ettcUi->generateHtml();
