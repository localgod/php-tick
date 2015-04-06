<?php
/**
 * Entity test extension
 *
 * PHP version 5.2
 *
 * @category ActiveEntity
 * @package  Tick
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
 use Tick\Entity;
/**
 * Entity test extension
 *
 * @category ActiveEntity
 * @package  Tick
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 *
 * @property integer(11) id			user_id		 - unique
 * @property string(255) name		full_name	 - <Jane> null
 * @property string(128) username  email		 - <Doe Doe>
 * @property DateTime	 created
 * @property DateTime	 updated				 - null
 *
 * @collection entity_extension_collection
 * @connection entity_extension_connection
 */
class EntityExtension extends Entity {
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

	/**
	 * List properties on object
	 *
	 * @return array with property names
	 * @see Entity::listPropertyNames()
	 */
	public function listPropertyNames() {
		return parent::listPropertyNames();
	}

	/**
	 * Check if a property exists
	 *
	 * @param string $name Name of the property to check
	 *
	 * @return boolean
	 * @see Entity::propertyExists()
	 */
	public function propertyExists($name) {
		return parent::propertyExists($name);
	}

	/**
	 * Check if a property exists
	 *
	 * @param string $name Name of the property to check
	 *
	 * @return boolean
	 * @see Entity::propertyAlias()
	 */
	public function propertyAlias($name) {
		return parent::propertyAlias($name);
	}

	/**
	 * Get property default value
	 *
	 * @param string $name Property name
	 *
	 * @return mixed Property default value if specified
	 * @see Entity::defaultValue()
	 */
	public function defaultValue($name) {
		return parent::defaultValue($name);
	}

	/**
	 * Disallow the value to be persisted if null
	 *
	 * @param string $name Property name
	 *
	 * @return boolean false if the property can be persisted with a null value
	 * @see Entity::notNull()
	 */
	public function notNull($name) {
		return parent::notNull($name);
	}

	/**
	 * Get field alias
	 *
	 * @param string $name Field name
	 *
	 * @return string Field alias or field name if no alias was found/specfied
	 * @throws RuntimeException if field could not be matched to a property
	 * @see Entity::fieldAlias()
	 */
	public function fieldAlias($name) {
		return parent::fieldAlias($name);
	}

	/**
	 * Is property unique
	 *
	 * @param string $name Field name
	 *
	 * @return boolean Property is unique
	 * @throws RuntimeException if property don't exists
	 * @see Entity::_mustBeUnique()
	 */
	public function mustBeUnique($name) {
		return parent::mustBeUnique($name);
	}

	/**
	 * Get the property type
	 *
	 * @param string $name Property name
	 *
	 * @return string
	 * @see Entity::propertyType()
	 */
	public function propertyType($name) {
		return parent::propertyType($name);
	}

	/**
	 * Check if the value fits the size of the property if the size is specified
	 *
	 * @param string $name  Name of the property
	 * @param mixed  $value Value to test
	 *
	 * @return boolean
	 * @see Entity::_isValidLength()
	 */
	public function _isValidLength($name, $value) {
		return parent::isValidLength($name, $value);
	}

	/**
	 * Get meta data
	 *
	 * @throws InvalidArgumentException if an invalid option was found
	 *
	 * @return array
	 * @see Entity::getMetadata()
	 */
	public function _getMetadata() {
		return parent::getMetadata();
	}
}