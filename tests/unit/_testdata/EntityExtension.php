<?php
/**
 * Entity test extension
 *
 * PHP version >=8.0
 *
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
 use Localgod\Tick\Entity;

/**
 * Entity test extension
 *
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 *
 * @property integer(11) id         user_id      - unique
 * @property string(255) name       full_name    - <Jane> null
 * @property string(128) username  email         - <Doe Doe>
 * @property DateTime    created
 * @property DateTime    updated                 - null
 *
 * @collection entity_extension_collection
 * @connection entity_extension_connection
 */
class EntityExtension extends Entity
{
    /**
     * Get collection name
     *
     * @return string
     * @see Entity::getCollectionName()
     */
    public function getCollectionName(): string
    {
        return parent::getCollectionName();
    }

    /**
     * Get connection name
     *
     * @return string
     * @see Entity::getConnectionName()
     */
    public function getConnectionName(): string
    {
        return parent::getConnectionName();
    }

    /**
     * List properties on object
     *
     * @return array with property names
     * @see Entity::listPropertyNames()
     */
    public function listPropertyNames(): array
    {
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
    public function propertyExists(string $name): bool
    {
        return parent::propertyExists($name);
    }

    /**
     * Check if a property exists
     *
     * @param string $name Name of the property to check
     *
     * @return string
     * @see Entity::propertyAlias()
     */
    public function propertyAlias(string $name): string
    {
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
    public function defaultValue(string $name): mixed
    {
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
    public function notNull(string $name): bool
    {
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
    public function fieldAlias(string $name): string
    {
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
    public function mustBeUnique(string $name): bool
    {
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
    public function propertyType(string $name): string
    {
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
    public function _isValidLength(string $name, mixed $value): bool
    {
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
    public function _getMetadata(): array
    {
        return parent::getMetadata();
    }
}
