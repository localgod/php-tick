<?php
/**
 * Tick test extension with other connection settings
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Tick;

/**
 * Tick test extension
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 *
 * @collection tick_collection
 * @connection tick_connection
 *
 * @property integer(11) id        tick_collection_id   - unique
 * @property float       latitude  latitude             - null
 * @property float       longitude longitude            - null
 * @property float       color     color                - null
 */
class TickExtension2 extends Tick
{
}
