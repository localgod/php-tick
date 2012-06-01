<?php
/**
 * Result
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-04-09
 */
/**
 * Result
 *
 * The result class helps you query your model and manage result sets
 *
 * @category ActiveRecord
 * @package  Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-11-15
 */
class Result implements Iterator {
	/**
	 * The default max result we get as a safety precaution
	 */
	const DEFAULT_LIMIT = 10000;

	/**
	 * Position in result set
	 * @var integer
	 */
	private $_position = 0;

	/**
	 * Condintions
	 * @var array
	 */
	private $_conditions;

	/**
	 * Limit value
	 * @var integer
	 */
	private $_limit;

	/**
	 * Offset value
	 * @var integer
	 */
	private $_offset;

	/**
	 * List of object
	 * @var array
	 */
	private $_result;

	/**
	 * Instance of the model we query
	 * @var Object
	 */
	private $_model;

	/**
	 * Order of result
	 * @var array
	 */
	private $_order;

	/**
	 * Direction of result
	 * @var boolean
	 */
	private $_direction;

	/**
	 * Construct a new result
	 *
	 * @param Object $model Model to get results from
	 *
	 * @return void
	 */
	public function __construct($model) {
		$this->_model = new $model;
		$this->_position = 0;
		$this->_conditions = array();
		$this->_order = array();
		$this->_offset = 0;
	}

	/**
	 * Get result
	 *
	 * @return array
	 */
	private function _getResult() {
		if (!isset($this->_result)) {
			$fieldNames = $this->_model->listFieldNames();
			$this->_result = $this->_model->getStorage()->get($this->_model->getCollectionName(), $fieldNames, $this->_conditions, $this->_order, $this->_direction, $this->_limit, $this->_offset);
		}
		return $this->_result;
	}

	/**
	 * Count elements in result
	 *
	 * @return integer
	 */
	public function count() {
		if (isset($this->_result) || isset($this->_limit)) {
			return count($this->_getResult());
		} else {
			return $this->_model->getStorage()->count($this->_model->getCollectionName(), $this->_conditions);
		}
	}

	/**
	 * Are there elements in result?
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return $this->count() == 0;
	}

	/**
	 * Create criteria
	 *
	 * @param string $property  Property
	 * @param string $condition Condition
	 * @param mixed  $value	    Value
	 *
	 * @return Result
	 */
	public function where($property, $condition, $value) {
		$this->_conditions[] = array('property' => $this->_model->propertyAlias($property), 'condition' => $condition, 'value' => $value);
		return $this;
	}

	/**
	 * Create equals criteria
	 *
	 * @param string $property Property
	 * @param mixed  $value    Value
	 *
	 * @return Result
	 */
	public function whereEquals($property, $value) {
		$this->_conditions[] = array('property' => $this->_model->propertyAlias($property), 'condition' => '=', 'value' => $value);
		return $this;
	}

	/**
	 * Create between criteria
	 *
	 * @param string $property Property
	 * @param mixed  $valueOne Value one
	 * @param mixed  $valueTwo Value two
	 *
	 * @return Result
	 */
	public function whereBetween($property, $valueOne, $valueTwo) {
		$this->_conditions[] = array('property' => $this->_model->propertyAlias($property), 'condition' => '>', 'value' => $valueOne);
		$this->_conditions[] = array('property' => $this->_model->propertyAlias($property), 'condition' => '<', 'value' => $valueTwo);
		return $this;
	}

	/**
	 * Create a loose string match criteria on all fields.
	 *
	 * @param string $string String
	 *
	 * @return Result
	 */
	public function whereAnyMatches($string) {
		$this->_conditions[] = array('property' => '*', 'condition' => 'MATCHES', 'value' => $string);

		return $this;
	}

	/**
	 * Set an order clause for det result
	 *
	 * @param array|string $properties Properties to order by
	 * @param boolean	   $direction  Direction of the order (true = ascending, false descending)
	 *
	 * @return Result
	 */
	public function orderBy($properties, $direction = true) {
		if (!is_bool($direction)) {
			throw new InvalidArgumentException('Order direction must be boolean. (true = ascending, false descending)');
		}
		$this->_direction = $direction;
		if (is_array($properties)) {
			foreach ($properties as $property) {
				$this->_order[] = $this->_model->propertyAlias($property);
			}
		} else {
			$this->_order[] = $this->_model->propertyAlias($properties);
		}
		return $this;
	}

	/**
	 * Limit the result set
	 *
	 * @param integer $limit Limit
	 *
	 * @return Result
	 */
	public function limit($limit) {
		$this->_limit = $limit;
		return $this;
	}

	/**
	 * Offset the result set
	 *
	 * @param integer $offset Offset
	 *
	 * @return Result
	 */
	public function offset($offset) {
		$this->_offset = $offset;
		if (!is_numeric($this->_limit) && !$this->_limit >= 0) {
			$this->_limit = self::DEFAULT_LIMIT; //This is a arbitrary number!
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
	public function rewind() {
		$this->_getResult();
		$this->_position = 0;
	}

	/**
	 * Get the current
	 *
	 * @return void
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->getModel($this->_position);
	}

	/**
	 * Get the current key
	 *
	 * @return integer
	 * @see Iterator::key()
	 */
	public function key() {
		$this->_getResult();
		return $this->_position;
	}

	/**
	 * Set the key to the next element in the result
	 *
	 * @return void
	 * @see Iterator::next()
	 */
	public function next() {
		$this->_getResult();
		++$this->_position;
	}

	/**
	 * Check if the position in the result set is valid
	 *
	 * @return boolean
	 * @see Iterator::valid()
	 */
	public function valid() {
		$this->_getResult();
		return isset($this->_result[$this->_position]);
	}

	/**
	 * Get model
	 *
	 * @param integer $position Position in result
	 *
	 * @return Object
	 */
	private function getModel($position) {
		$this->_getResult();
		$class = get_class($this->_model);
		$entity = new $class();
		$meta = $this->_model->getMetadata();
		$fields = $meta["fields"];

		if (!$this->isEmpty()) {
			foreach ($this->_result[$position] as $field => $value) {
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