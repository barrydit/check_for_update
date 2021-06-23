<?php
spl_autoload_register(function ($class_name) {
	$preg_match = preg_match('/^CzProject\\\GitPhp\\\/', $class_name);

	if (1 === $preg_match) {
		$class_name = preg_replace('/\\\/', '/', $class_name);
		//$class_name = preg_replace('/^CzProject\\/GitPhp\\//', '', $class_name);    
		require_once(__DIR__ . DIRECTORY_SEPARATOR . $class_name . '.php');
	}
});