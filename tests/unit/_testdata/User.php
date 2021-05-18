<?php
/**
 * User
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Tick;

/**
 * User
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 *
 * @collection users
 * @property integer(11) id        user_id    - unique
 * @property string(255) firstname first_name - <Jane>
 * @property string(255) lastname  last_name  - <Doe Doe>
 * @property DateTime    created   created
 * @property integer(1)  owner     owner      - null
 * @property float(1)    latitude  latitude   - null
 * @property float(1)    longitude longitude  - null
 */
class User extends Tick
{
    /**
     * Get user by id
     *
     * @param integer $userId User id
     *
     * @return User
     */
    public static function getById($userId = null)
    {
        if ($userId) {
            $user = new self();
            if ($user->get()->where('id', '=', $userId)->count() > 0) {
                return $user->get()->where('id', '=', $userId)->current();
            }
        }
        return null;
    }
}
