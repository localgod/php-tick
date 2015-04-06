<?php
/**
 * Tick test extension with other connection settings
 *
 * PHP version >=5.3.3
 *
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Tick;

/**
 * Tick test extension
 *
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 *
 * @collection tick_collection
 *
 * @property integer(11) id        tick_collection_id   - unique
 * @property float       latitude  latitude				- null
 * @property float       longitude longitude			- null
 */
class TickExtension extends Tick {
}