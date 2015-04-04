<?php

/**
 * Entity
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Entity
 *
 * The entity class manages none storage related functionality in tick.
 * Basically this means working with the properties of your object.
 *
 * @category ActiveRecord
 * @package Tick
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
abstract class Entity extends Type
{

    /**
     * Has the object been modified
     *
     * @var boolean
     */
    private $modified = false;

    /**
     * Connection name
     * 
     * @var array
     */
    private static $connectionNameMap = array();

    /**
     * Collection name
     * 
     * @var array
     */
    private static $collectionNameMap = array();

    /**
     * Property map
     *
     * @var array map of properties from Document Comment
     */
    private static $propertyMap = array();

    /**
     * Initialize the properties with empty values
     *
     * @return void
     */
    protected function init()
    {
        $meta = $this->getMetadata();
        foreach ($meta["properties"] as $name => $prop) {
            $this->$name = $prop["default"];
        }
    }

    /**
     * Get connection name
     *
     * @return string
     */
    public function getConnectionName()
    {
        if (! key_exists(get_class($this), self::$connectionNameMap)) {
            $regExp = '/@connection[[:blank:]]+([a-zA-Z0-9_]+)/';
            
            if (preg_match($regExp, $this->getClassComment(), $mathes)) {
                $this->setConnectionName($mathes[1]);
                return $mathes[1];
            } else {
                return TickManager::DEFAULT_CONNECTION_NAME;
            }
        }
        return self::$connectionNameMap[get_class($this)];
    }

    /**
     * Set connection name
     *
     * @param string $connectionName
     *            Connection name
     *            
     * @return void
     */
    public function setConnectionName($connectionName)
    {
        self::$connectionNameMap[get_class($this)] = $connectionName;
        $this->_resetStorage();
    }

    /**
     * Get collection name
     *
     * @return string
     */
    public function getCollectionName()
    {
        if (! key_exists(get_class($this), self::$collectionNameMap)) {
            $regExp = '/@collection[[:blank:]]+([a-zA-Z0-9_]+)/';
            if (preg_match($regExp, $this->getClassComment(), $mathes)) {
                $this->setCollectionName($mathes[1]);
                return $mathes[1];
            } else {
                throw new LogicException('No @collection tag defined');
            }
        }
        return self::$collectionNameMap[get_class($this)];
    }

    /**
     * Set collection name
     *
     * @param string $collectionName
     *            Collection name
     *            
     * @return void
     */
    public function setCollectionName($collectionName)
    {
        self::$collectionNameMap[get_class($this)] = $collectionName;
    }

    /**
     * Set collection name
     *
     * @return void
     */
    public function resetCollectionName()
    {
        unset(self::$collectionNameMap[get_class($this)]);
    }

    /**
     * Call method by name
     *
     * @param string $name
     *            of method to call
     * @param array $arguments
     *            Arguments to method
     *            
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^get.*/', $name)) {
            return $this->callGet($name, $arguments);
        } else {
            if (preg_match('/^set.*/', $name)) {
                return $this->callSet($name, $arguments);
            }
        }
    }

    /**
     * Get the object state as a array
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $properties = $this->listPropertyNames();
        foreach ($properties as $property) {
            switch ($this->propertyType($property)) {
                case 'integer':
                    $result[$property] = (integer) $this->$property;
                    break;
                case 'DateTime':
                    $result[$property] = $this->$property;
                    break;
                case 'float':
                    $result[$property] = (float) $this->$property;
                    break;
                case 'string':
                    $result[$property] = (string) $this->$property;
                default:
                    $result[$property] = $this->$property;
            }
        }
        return $result;
    }

    /**
     * Get the property
     *
     * @param string $name
     *            Name of the property
     *            
     * @return mixed
     * @throws Exception if not found
     */
    public function __get($name)
    {
        if (in_array($name, $this->listPropertyNames())) {
            return $this->$name;
        }
        throw new Exception('Tried to access unknown property: ' . $name);
    }

    /**
     * String representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        $out = '';
        foreach ($this->listPropertyNames() as $property) {
            if ($out != '') {
                $out .= ', ';
            }
            if (is_object($this->$property) && get_class($this->$property) == 'DateTime') {
                $date = $this->$property;
                $date->format('Y-m-d H:i:s');
                $out .= $property . ' => ' . $date->format('Y-m-d H:i:s');
            } else {
                $out .= $property . ' => ' . $this->$property;
            }
        }
        return $out;
    }

    /**
     * Call get method
     *
     * @param string $name
     *            of method to call
     * @param array $arguments
     *            Arguments to method
     *            
     * @return mixed
     */
    private final function callGet($name, $arguments)
    {
        $propertyName = self::lcfirst(str_replace('get', '', $name));
        
        if (! $this->propertyExists($propertyName)) {
            $message = 'Call to undefined method ' . get_class($this) . '::' . $name;
            throw new BadMethodCallException($message);
        }
        
        if (sizeof($arguments) != 0) {
            $message = 'Method ' . get_class($this) . '::' . $name . ' does not take any arguments';
            throw new InvalidArgumentException($message);
        }
        
        return $this->$propertyName;
    }

    /**
     * Is the object modified
     *
     * If true is given as an argument the object will be marked as modified
     *
     * @param boolean $modified
     *            Set the object as modified
     *            
     * @return boolean
     */
    protected final function modified($modified = false)
    {
        if ($modified) {
            $this->modified = true;
        }
        return $this->modified;
    }

    /**
     * Call set method
     *
     * @param string $name
     *            of method to call
     * @param array $arguments
     *            Arguments to method
     *            
     * @return mixed
     */
    private final function callSet($name, $arguments)
    {
        $propertyName = self::lcfirst(str_replace('set', '', $name));
        
        if (! $this->propertyExists($propertyName)) {
            $message = 'Call to undefined method ' . get_class($this) . '::' . $name;
            throw new BadMethodCallException($message);
        }
        
        if (sizeof($arguments) != 1) {
            $message = 'Method ' . get_class($this) . '::' . $name . ' takes only one arguments';
            throw new InvalidArgumentException($message);
        }
        
        if ($arguments[0] == null || $this->isValidType($propertyName, $arguments[0])) {
            if ($this->$propertyName != $arguments[0]) {
                $this->$propertyName = $arguments[0];
                $this->modified(true);
            }
            return $this;
        }
    }

    /**
     * Check if a property exists
     *
     * @param string $name
     *            Name of the property to check
     *            
     * @return boolean
     */
    protected function propertyExists($name)
    {
        $prop = $this->getMetadata();
        
        return array_key_exists($name, $prop["properties"]) ? true : false;
    }

    /**
     * List properties on object
     *
     * @return array with property names
     */
    public function listPropertyNames()
    {
        $prop = $this->getMetadata();
        return array_keys($prop["properties"]);
    }

    /**
     * List fields on object
     *
     * @return array with field names
     */
    public function listFieldNames()
    {
        $prop = $this->getMetadata();
        return array_keys($prop["fields"]);
    }

    /**
     * Get property alias
     *
     * @param string $name
     *            Property name
     *            
     * @return string Property alias or property name if no alias was found/specfied
     */
    public function propertyAlias($name)
    {
        $prop = $this->getMetadata();
        return $prop["properties"][$name]["field"];
    }

    /**
     * Get property default value
     *
     * @param string $name
     *            Property name
     *            
     * @return mixed Property default value if specified
     */
    protected function defaultValue($name)
    {
        $prop = $this->getMetadata();
        return $prop["properties"][$name]["default"];
    }

    /**
     * Get property
     *
     * @param string $name
     *            Name
     * @param string $key
     *            Key
     *            
     * @return string
     */
    protected function getProperty($name, $key)
    {
        $prop = $this->getMetadata();
        if (key_exists($key, $prop["properties"][$name])) {
            return $prop["properties"][$name][$key];
        } else {
            return null;
        }
    }

    /**
     * Disallow the value to be persisted if null
     *
     * @param string $name
     *            Property name
     *            
     * @return boolean false if the property can be persisted with a null value
     */
    protected function notNull($name)
    {
        return $this->getProperty($name, "null");
    }

    /**
     * Get field alias
     *
     * @param string $field
     *            Field name
     *            
     * @return string Field alias or field name if no alias was found/specfied
     * @throws RuntimeException if field could not be matched to a property
     */
    protected function fieldAlias($field)
    {
        $prop = $this->getMetadata();
        if (key_exists("property", $prop["fields"][$field])) {
            return $prop["fields"][$field]["property"];
        }
        throw new RuntimeException('Field "' . $field . '" has no property match');
    }

    /**
     * Is property unique
     *
     * @param string $name
     *            Field name
     *            
     * @return boolean Property is unique
     * @throws RuntimeException if property don't exists
     */
    protected function mustBeUnique($name)
    {
        return $this->getProperty($name, "unique");
    }

    /**
     * Check if the value fits the size of the property if the size is specified
     *
     * @param string $name
     *            Name of the property
     * @param mixed $value
     *            Value to test
     *            
     * @return boolean
     * @throws RangeException if the value dos not fit the specified size
     */
    protected function isValidLength($name, $value)
    {
        $size = $this->getProperty($name, "size");
        if (isset($size) && $size > 0) {
            if (strlen($value) > $size) {
                throw new RangeException('Should be in the range 0-' . $size . ' characters. Was ' . strlen($value) . '.');
            }
        }
        return true;
    }

    /**
     * Get the property type
     *
     * @param string $name
     *            Property name
     *            
     * @return string
     */
    protected function propertyType($name)
    {
        $prop = $this->getMetadata();
        return $prop["properties"][$name]["type"];
    }

    /**
     * Lowercase first letter in string
     *
     * @param string $string
     *            String to lowercase
     *            
     * @return string
     */
    protected static final function lcfirst($string)
    {
        return substr_replace($string, strtolower(substr($string, 0, 1)), 0, 1);
    }

    /**
     * Get meta data
     *
     * @throws InvalidArgumentException if an invalid option was found
     *        
     * @return array
     */
    public function getMetadata()
    {
        $class = get_class($this);
        if (! key_exists($class, self::$propertyMap)) {
            $properties = array();
            $propertyBasePattern = '/.*@property\s+([a-zA-Z0-9]+)(?:\(([0-9]+)\))?\s+([_a-zA-Z0-9]+)/';
            $optionsPattern = '/(?P<default><[^>]+>)|(?P<options>[a-z]+)/';
            foreach (explode("\n", $this->getClassComment()) as $line) {
                if (preg_match($propertyBasePattern, $line, $mathes)) {
                    
                    $type = $mathes[1];
                    $size = (int) $mathes[2];
                    $name = $mathes[3];
                    $property = array(
                        "type" => $type,
                        "default" => null
                    );
                    
                    $rest = trim(substr($line, strlen($mathes[0]), 1000));
                    if (empty($rest) || $rest[0] == "-") {
                        $property["field"] = $name;
                    } else {
                        preg_match("/[_a-zA-Z0-9]+/", $rest, $mathes2);
                        $property["field"] = $mathes2[0];
                        $rest = trim(substr($rest, strlen($mathes2[0])));
                    }
                    
                    if ($size > 0) {
                        $property["size"] = $size;
                    }
                    
                    if (preg_match_all($optionsPattern, $rest, $mathes, PREG_PATTERN_ORDER)) {
                        foreach ($mathes["default"] as $option) {
                            if (! empty($option)) {
                                $property["default"] = substr($option, 1, - 1);
                                break;
                            }
                        }
                        foreach ($mathes["options"] as $option) {
                            if (! empty($option)) {
                                if (in_array($option, array(
                                    "null",
                                    "unique"
                                ))) {
                                    $property[$option] = true;
                                } else {
                                    throw new InvalidArgumentException("$class::$name option '$option' is not a valid option");
                                }
                            }
                        }
                    }
                    
                    $properties[$name] = $property;
                }
            }
            
            $fields = array();
            foreach ($properties as $name => $props) {
                $fields[$props["field"]] = array_merge($props, array(
                    "property" => $name
                ));
            }
            
            self::$propertyMap[$class] = array(
                "properties" => $properties,
                "fields" => $fields
            );
        }
        
        return self::$propertyMap[$class];
    }
}