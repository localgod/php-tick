<?php

// First we make sure we don't mess with other autoloaders
if (false === spl_autoload_functions()) {
	if (function_exists('__autoload')) {
		spl_autoload_register('__autoload', false);
	}
}

// Then we setup the Tick autoloader
require_once dirname(__FILE__) . '/src/Tick/Tick.php';
spl_autoload_register(array('Tick', 'autoload'));

?>