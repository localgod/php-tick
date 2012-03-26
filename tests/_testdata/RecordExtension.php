<?php
/**
 * Record test extension
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	  http://code.google.com/p/php-tick/ php-tick
 * @since	 2011-11-11
 */
/**
 * Record test extension
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	  http://code.google.com/p/php-tick/ php-tick
 * @since	 2011-11-11
 *
 * @collection record_extension_collection
 * @connection record_extension_connection
 */
class RecordExtension extends Record {
	public function getCollectionName() {
		return parent::getCollectionName();
	}

	public function getConnectionName() {
		return parent::getConnectionName();
	}
}