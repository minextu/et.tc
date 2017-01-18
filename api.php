<?php namespace Minextu\EttcApi;

use Minextu\Ettc\Ettc;

require_once("src/autoload.php");

session_start();

$ettc = new Ettc;
$rootDir = dirname($_SERVER['SCRIPT_NAME']) . "/api";
EttcApi::run($rootDir, $ettc->getDb());
