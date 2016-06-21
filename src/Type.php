<?php
namespace Localgod\Tick;

/**
 * Type handler for Tick
 *
 * PHP version >=5.3.3
 *
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
 use \ReflectionClass;
 use \InvalidArgumentException;

/**
 * Type handler for Tick
 *
 * This class manages types in Tick. PHP's automatic type conversion is
 * not always convenient when talking to a data storage, so we attempt to
 * "force" some kind of type checking and conversion for values handled by
 * Tick.
 *
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
abstract class Type
{
    /**
     * Class comment
     * @var string
     */
    private $classComment;

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
    protected function isValidType($property, $value)
    {
        $type = $this->propertyType($property);
        $value = is_numeric($value) ? $value + 1 - 1 : $value;//Force to be a number

        if ($type == 'integer' && is_numeric($value) && preg_match('/^[0-9]+$/', $value)) {
            $this->isValidLength($property, $value);
            return true;
        } elseif ($type == 'float' && is_float($value)) {
            return true;
        } elseif ($type == 'double' && is_double($value)) {
            return true;
        } elseif ($type == 'boolean' && is_bool($value)) {
            return true;
        } elseif ($type == 'string') {
            if (is_string($value) || is_float($value) || is_integer($value) || is_bool($value)) {
                $this->isValidLength($property, $value);
                return true;
            }
        } elseif ($type == 'array' && is_array($value)) {
            return true;
        } elseif ($type == 'mixed') {
            return true;
        } elseif ($value instanceof $type) {
            return true;
        } else {
            $message = 'Input:\''.$value.'\' of type \''.gettype($value).
            '\' does not match property declartion ['.$type.' $'.$property.']';
            throw new InvalidArgumentException($message, 1);
        }
    }
    /**
     * Get class comment
     *
     * @return string
     */
    protected function getClassComment()
    {
        if ($this->classComment == '') {
            $data = new ReflectionClass(get_class($this));
            $this->classComment = $data->getDocComment();
        }
        return $this->classComment;
    }
}
