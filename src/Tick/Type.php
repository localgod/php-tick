<?php
/**
 * Type handler for Tick
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 * @since    2011-04-09
 */
/**
 * Type handler for Tick
 *
 * This class manages types in Tick. PHP's automatic type conversion is
 * not always convenient when talking to a data storage, so we attempt to
 * "force" some kind of type checking and conversion for values handled by
 * Tick.
 *
 * @category ActiveRecord
 * @package  Tick
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 * @since    2011-04-17
 */
abstract class Type {
	/**
	 * Class comment
	 * @var string
	 */
	private $_classComment;

	/**
	 * Check if value is valid for the given property
	 *
	 * @param string $property Name of property
	 * @param mixed  $value    Value to test
	 *
	 * @return boolean
	 * @throws RangeException if the value dos not fit the specified size
	 * @throws InvalidArgumentException if the value dos not mathc the  property declartion
	 */
	protected function isValidType($property, $value) {
		$type = $this->propertyType($property);
		$value = is_numeric($value) ? $value + 1 - 1 : $value;//Force to be a number

		if ($type == 'integer' && is_numeric($value) && preg_match('/^[0-9]+$/', $value)) {
			$this->_isValidLength($property, $value);
			return true;
		} else if ($type == 'float' && is_float($value)) {
			return true;
		} else if ($type == 'double' && is_double($value)) {
			return true;
		} else if ($type == 'boolean' && is_bool($value)) {
			return true;
		} else if ($type == 'string') {
			if (is_string($value) || is_float($value) || is_integer($value) || is_bool($value)) {
				$this->_isValidLength($property, $value);
				return true;
			}
		} else if ($type == 'array' && is_array($value)) {
			return true;
		} else if ($type == 'mixed') {
			return true;
		} else if ($value instanceof $type) {
			return true;
		} else {
			$message = 'Input:\''.$value.'\' of type \''.gettype($value).'\' does not match property declartion ['.$type.' $'.$property.']';
			throw new InvalidArgumentException($message, 1);
		}
	}
	/**
	 * Get class comment
	 *
	 * @return string
	 */
	protected function getClassComment() {
		if ($this->_classComment == '') {
			$data = new ReflectionClass(get_class($this));
			$this->_classComment = $data->getDocComment();
		}
		return $this->_classComment;
	}

}