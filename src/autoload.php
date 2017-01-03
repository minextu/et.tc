<?php
require_once(__DIR__.'/../vendor/autoload.php');

function autoload($className)
{
	$className = str_replace('\\', '/', $className);
	$file = __DIR__.'/'.$className . '.php';

	if (!is_file($file))
		return false;

	include($file);
}
spl_autoload_register('autoload');

?>
