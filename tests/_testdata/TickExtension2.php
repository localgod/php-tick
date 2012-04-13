<?php
/**
 * Tick test extension with other connection settings
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
 * Tick test extension
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-11-11
 *
 * @collection tick_collection 
 * @connection tick_connection
 *
 * @property integer(11) id        tick_collection_id   - unique
 * @property float       latitude  latitude             - null
 * @property float       longitude longitude            - null
 * @property float       color     color                - null
 */
class TickExtension2 extends Tick {
}