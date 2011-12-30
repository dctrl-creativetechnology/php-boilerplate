<?php

// Include functionality we cannot autoload
require_once __DIR__.'/../vendor/boilerplate/src/Boilerplate/Component/Autoloader/Universal.php';

// Intantiate the Autoloader
$loader = new Boilerplate\Component\Autoloader\Universal;

// Register namespaced classes
$loader->registerNamespaces(array(
	'Boilerplate' => __DIR__.'/../vendor/boilerplate/src',
));

// Register namespace fallbacks
$loader->registerNamespaceFallbacks(array(
	__DIR__.'/src',
));

// Register the Autoloader to the SPL autoload stack
$loader->register();