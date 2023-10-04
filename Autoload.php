<?php
class Autoload
{
	public function __construct()
	{
		spl_autoload_register(array($this, 'load'));
	}
	public function load($className)
	{
		$className = str_replace('/', DIRECTORY_SEPARATOR, $className);
		$className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
		require_once __DIR__ . DIRECTORY_SEPARATOR . $className . '.php'; 
	}
}