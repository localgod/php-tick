<?php
/**
 * Tick Manager test extension
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-11-11
 */
/**
 * Tick Manager  test extension 
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-11-11
 */
class TickManagerExtension extends TickManager {
	/**
	 * The unigue name of connection in $GLOBALS
	 *
	 * @param string $name Connection name
	 *
	 * @return string unique name
	 */
	public static function getUniqueName($name) {
		return parent::getUniqueName($name);
	}
}