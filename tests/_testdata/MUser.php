<?php
/**
 * User
 *
 * PHP version 5.2
 *
 * @category Test
 * @package  Test
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Tick;
/**
 * User
 *
 * @category Test
 * @package  Test
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 *
 * @collection users
 * 
 * @property string      id        _id        - unique
 * @property string(255) firstname first_name - <Jane> null
 * @property string(255) lastname  last_name  - <Doe Doe>  
 * @property DateTime    created   created    
 * @property integer(1)  owner     owner      - null
 * @property float(1)    latitude  latitude   - null
 * @property float(1)    longitude longitude  - null
 */
class MUser extends Tick {
	/**
	 * Get user by id
	 *
	 * @param integer $userId User id
	 *
	 * @return User
	 */
	public static function getById($userId = null) {
		if ($userId) {
			$user = new self();
			$criteria = $user->createCriteria('id', '=', $userId);
			$result = $user->get(array($criteria));
			if (!empty($result)) {
				return $result[0];
			}
		}
		return null;
	}
}