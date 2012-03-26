<?php
/**
 * Entity test extension
 *
 * PHP version 5.2
 *
 * @category ActiveEntity
 * @package  Tick
 * @author	Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	  http://code.google.com/p/php-tick/ php-tick
 * @since	 2011-11-11
 */
/**
 * Entity test extension
 *
 * @category ActiveEntity
 * @package  Tick
 * @author	Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	  http://code.google.com/p/php-tick/ php-tick
 * @since	 2011-11-11
 *
 * @property integer(11) id			user_id		 -	unique
 * @property string(255) name		full_name	 -<Jane> null
 * @property string(128) username  email			 - <Doe Doe>
 * @property DateTime	 created
 * @property DateTime	 updated					 - null
 *
 * @collection entity_extension_collection
 * @connection entity_extension_connection
 */
class EntityExtension extends Entity {
	public function getCollectionName() {
		return parent::getCollectionName();
	}

	public function getConnectionName() {
		return parent::getConnectionName();
	}

	public function listPropertyNames() {
		return parent::listPropertyNames();
	}

	public function propertyExists($name) {
		return parent::propertyExists($name);
	}

	public function propertyAlias($name) {
		return parent::propertyAlias($name);
	}

	public function _defaultValue($name) {
		return parent::_defaultValue($name);
	}

	public function _Null($name) {
		return parent::_Null($name);
	}

	public function fieldAlias($name) {
		return parent::fieldAlias($name);
	}

	public function _mustBeUnique($name) {
		return parent::_mustBeUnique($name);
	}

	public function propertyType($name) {
		return parent::propertyType($name);
	}

	public function _isValidLength($name, $value) {
		return parent::_isValidLength($name, $value);
	}

	public function _getMetadata() {
		return parent::getMetadata();
	}
}