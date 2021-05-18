<?php
/**
 * Tick Manager test extension
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;

/**
 * Tick Manager  test extension
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
class TickManagerExtension extends Manager
{
    /**
     * The unigue name of connection in $GLOBALS
     *
     * @param string $name Connection name
     *
     * @return string unique name
     */
    public static function getUniqueName($name)
    {
        return parent::getUniqueName($name);
    }
}
