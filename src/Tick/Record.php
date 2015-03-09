<?php

/**
 * Record
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Record
 *
 * The record class adds storage related functionality to the Tick objects.
 *
 * @category ActiveRecord
 * @package Tick
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
abstract class Record extends Entity
{

    /**
     * Storage
     *
     * @var Storage
     */
    private $_storage;

    /**
     * Get storage
     *
     * @return Storage
     */
    public function getStorage()
    {
        if (! $this->_storage instanceof Storage) {
            $this->_storage = TickManager::getStorage($this->getConnectionName());
        }
        return $this->_storage;
    }

    /**
     * Reset storage
     *
     * @return void
     */
    private function _resetStorage()
    {
        $this->_storage = null;
    }

    /**
     * Prepare data for storage
     *
     * Nor sure this is a good name for the method
     *
     * @param boolean $insert
     *            If we use the output for insert unique field can be empty
     *            
     * @return array
     * @throws RuntimeException if property can not be persited as null
     */
    protected function hydrate($insert = true)
    {
        $data = array();
        foreach ($this->listPropertyNames() as $property) {
            if ($this->$property === null && ! $this->_Null($property) && ! $insert) {
                throw new RuntimeException('Property "' . $property . '" can not be persisted as null');
            }
            $data[$this->propertyAlias($property)] = array(
                'type' => $this->propertyType($property),
                'value' => $this->$property
            );
        }
        return $data;
    }

    /**
     * Create criteria
     *
     * @param string $property
     *            Property
     * @param string $condition
     *            Condition
     * @param mixed $value
     *            Value
     *            
     * @return array
     */
    public function createCriteria($property, $condition, $value)
    {
        return array(
            'property' => $property,
            'condition' => $condition,
            'value' => $value
        );
    }

    /**
     * Get list of object matching criterias
     *
     * @param array $criterias
     *            Key => Value pairs to search for
     *            
     * @return array of matching objects
     */
    protected function getSimple(array $criterias)
    {
        $result = array();
        foreach ($criterias as $key => $value) {
            $result[] = $this->createCriteria($key, '=', $value);
        }
        return $this->getAdvanced($result);
    }

    /**
     * Get list of object matching criterias
     *
     * @param array $criterias
     *            Key => Value pairs to search for
     *            
     * @return array of matching objects
     */
    protected function getAdvanced(array $criterias)
    {
        $result = array();
        foreach ($criterias as $criteria) {
            if (! $this->propertyExists($criteria['property'])) {
                throw new InvalidArgumentException('Unknown property used in get argument.');
            }
            $result[] = array(
                'property' => $this->propertyAlias($criteria['property']),
                'condition' => $criteria['condition'],
                'value' => $criteria['value']
            );
        }
        return $result;
    }

    /**
     * Get object unique criteria
     *
     * @return array
     */
    public function getUniqueCriteria()
    {
        $criterias = array();
        foreach ($this->listPropertyNames() as $property) {
            if ($this->_mustBeUnique($property)) {
                $criterias[] = $this->createCriteria($property, '=', $this->$property);
            }
        }
        return $criterias;
    }
}