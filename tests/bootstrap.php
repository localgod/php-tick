<?php
/**
 * php-tick active record library test bootstrap file
 *
 * PHP Version 5.1.2
 *
 * @category Utility
 * @package  Tick
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 * @since    2011-04-09
 */
if (false === spl_autoload_functions()) {
	if (function_exists('__autoload')) {
		spl_autoload_register('__autoload', false);
	}
}
require_once dirname(__FILE__).'/../src/Tick/Tick.php'; 
spl_autoload_register(array('Tick', 'autoload'));
