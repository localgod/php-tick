<?php
/**
 * Record test extension
 *
 * PHP version >=5.3.3
 *
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Record;

/**
 * Record test extension
 *
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 *
 * @collection record_extension_collection
 * @connection record_extension_connection
 */
class RecordExtension extends Record {
	/**
	 * Get collection name
	 *
	 * @return string
	 * @see Entity::getCollectionName()
	 */
	public function getCollectionName() {
		return parent::getCollectionName();
	}
	
	/**
	 * Get connection name
	 *
	 * @return string
	 * @see Entity::getConnectionName()
	 */
	public function getConnectionName() {
		return parent::getConnectionName();
	}
}