<?php
namespace Localgod\Tick;
/**
 * Result
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
 use \Iterator;
 use \DateTime;
/**
 * Result
 *
 * The result class helps you query your model and manage result sets
 *
 * @category ActiveRecord
 * @package Tick
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class Result implements Iterator
{

    /**
     * The default max result we get as a safety precaution
     */
    const DEFAULT_LIMIT = 10000;

    /**
     * Position in result set
     * 
     * @var integer
     */
    private $position = 0;

    /**
     * Condintions
     * 
     * @var array
     */
    private $conditions;

    /**
     * Limit value
     * 
     * @var integer
     */
    private $limit;

    /**
     * Offset value
     * 
     * @var integer
     */
    private $offset;

    /**
     * List of object
     * 
     * @var array
     */
    private $result;

    /**
     * Instance of the model we query
     * 
     * @var Object
     */
    private $model;

    /**
     * Order of result
     * 
     * @var array
     */
    private $order;

    /**
     * Direction of result
     * 
     * @var boolean
     */
    private $direction;

    /**
     * Construct a new result
     *
     * @param Object $model
     *            Model to get results from
     *            
     * @return void
     */
    public function __construct($model)
    {
        $this->model = new $model();
        $this->position = 0;
        $this->conditions = array();
        $this->order = array();
        $this->offset = 0;
    }

    /**
     * Get result
     *
     * @return array
     */
    private function getResult()
    {
        if (! isset($this->result)) {
            $fieldNames = $this->model->listFieldNames();
            $this->result = $this->model->getStorage()->get($this->model->getCollectionName(), $fieldNames, $this->conditions, $this->order, $this->direction, $this->limit, $this->offset);
        }
        return $this->result;
    }

    /**
     * Count elements in result
     *
     * @return integer
     */
    public function count()
    {
        if (isset($this->result) || isset($this->limit)) {
            return count($this->getResult());
        } else {
            return $this->model->getStorage()->count($this->model->getCollectionName(), $this->conditions);
        }
    }

    /**
     * Are there elements in result?
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() == 0;
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
     * @return Result
     */
    public function where($property, $condition, $value)
    {
        $this->conditions[] = array(
            'property' => $this->model->propertyAlias($property),
            'condition' => $condition,
            'value' => $value
        );
        return $this;
    }

    /**
     * Create equals criteria
     *
     * @param string $property
     *            Property
     * @param mixed $value
     *            Value
     *            
     * @return Result
     */
    public function whereEquals($property, $value)
    {
        $this->conditions[] = array(
            'property' => $this->model->propertyAlias($property),
            'condition' => '=',
            'value' => $value
        );
        return $this;
    }

    /**
     * Create between criteria
     *
     * @param string $property
     *            Property
     * @param mixed $valueOne
     *            Value one
     * @param mixed $valueTwo
     *            Value two
     *            
     * @return Result
     */
    public function whereBetween($property, $valueOne, $valueTwo)
    {
        $this->conditions[] = array(
            'property' => $this->model->propertyAlias($property),
            'condition' => '>',
            'value' => $valueOne
        );
        $this->conditions[] = array(
            'property' => $this->model->propertyAlias($property),
            'condition' => '<',
            'value' => $valueTwo
        );
        return $this;
    }

    /**
     * Create a loose string match criteria on all fields.
     *
     * @param string $string
     *            String
     *            
     * @return Result
     */
    public function whereAnyMatches($string)
    {
        $this->conditions[] = array(
            'property' => '*',
            'condition' => 'MATCHES',
            'value' => $string
        );
        
        return $this;
    }

    /**
     * Set an order clause for det result
     *
     * @param array|string $properties
     *            Properties to order by
     * @param boolean $direction
     *            Direction of the order (true = ascending, false descending)
     *            
     * @return Result
     */
    public function orderBy($properties, $direction = true)
    {
        if (! is_bool($direction)) {
            throw new InvalidArgumentException('Order direction must be boolean. (true = ascending, false descending)');
        }
        $this->direction = $direction;
        if (is_array($properties)) {
            foreach ($properties as $property) {
                $this->order[] = $this->model->propertyAlias($property);
            }
        } else {
            $this->order[] = $this->model->propertyAlias($properties);
        }
        return $this;
    }

    /**
     * Limit the result set
     *
     * @param integer $limit
     *            Limit
     *            
     * @return Result
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Offset the result set
     *
     * @param integer $offset
     *            Offset
     *            
     * @return Result
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        if (! is_numeric($this->limit) && ! $this->limit >= 0) {
            $this->limit = self::DEFAULT_LIMIT; // This is a arbitrary number!
            trigger_error('Limit was not specifically set, so Tick defaulted to ' . self::DEFAULT_LIMIT, E_USER_NOTICE);
        }
        return $this;
    }

    /**
     * Rewind the interator
     *
     * @return void
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->getResult();
        $this->position = 0;
    }

    /**
     * Get the current
     *
     * @return void
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->getModel($this->position);
    }

    /**
     * Get the current key
     *
     * @return integer
     * @see Iterator::key()
     */
    public function key()
    {
        $this->getResult();
        return $this->position;
    }

    /**
     * Set the key to the next element in the result
     *
     * @return void
     * @see Iterator::next()
     */
    public function next()
    {
        $this->getResult();
        ++ $this->position;
    }

    /**
     * Check if the position in the result set is valid
     *
     * @return boolean
     * @see Iterator::valid()
     */
    public function valid()
    {
        $this->getResult();
        return isset($this->result[$this->position]);
    }

    /**
     * Get model
     *
     * @param integer $position
     *            Position in result
     *            
     * @return Object
     */
    private function getModel($position)
    {
        $this->getResult();
        $class = get_class($this->model);
        $entity = new $class();
        $meta = $this->model->getMetadata();
        $fields = $meta["fields"];
        
        if (! $this->isEmpty()) {
            foreach ($this->result[$position] as $field => $value) {
                $property = $fields[$field]["property"];
                $propertyType = $fields[$field]["type"];
                
                if ($propertyType == 'DateTime') {
                    if ($value != 'NULL' && $value != '') {
                        $entity->$property = new DateTime($value);
                    } else {
                        $entity->$property = null;
                    }
                } else {
                    $entity->$property = $value;
                }
            }
            return $entity;
        } else {
            return null;
        }
    }
}