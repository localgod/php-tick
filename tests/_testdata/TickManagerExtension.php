<?php
/**
 * Tick Manager test extension
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
use Tick\TickManager;

/**
 * Tick Manager  test extension 
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
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