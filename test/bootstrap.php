<?php
include __DIR__.'/../vendor/autoload.php';

spl_autoload_register(function($class) {
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	if (is_file($file)) require_once $file;
});

