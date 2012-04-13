<?php
/**
 * Autoloader
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-04-09
 */
// First we make sure we don't mess with other autoloaders
if (false === spl_autoload_functions()) {
	if (function_exists('__autoload')) {
		spl_autoload_register('__autoload', false);
	}
}

// Then we setup the Tick autoloader
require_once dirname(__FILE__) . '/src/Tick/Tick.php';
spl_autoload_register(array('Tick', 'autoload'));