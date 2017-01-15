<?php namespace Minextu\EttcApi;

require_once("src/autoload.php");

session_start();

$rootDir = dirname($_SERVER['SCRIPT_NAME']) . "/api";
EttcApi::run($rootDir);
