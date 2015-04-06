<?php
namespace Tick;
/**
 * Tick
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */

/**
 * Tick
 *
 * Tick is THE class a object needs to extend to add Tick capabilities. It adds
 * basic CRUD operations.
 *
 * @category ActiveRecord
 * @package Tick
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
abstract class Tick extends Record
{

    /**
     * Path to Tick root directory
     *
     * @var string $path Tick root directory
     */
    private static $path;

    /**
     * Construct a new record
     *
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Save the current object state to persisten storage
     *
     * @return void
     */
    public function save()
    {
        if ($this->modified()) {
            if ($this->exists()) {
                $this->update();
            } else {
                $this->insert();
            }
        }
    }

    /**
     * Check an object exists with the unique criteria
     *
     * @return boolean
     */
    private function exists()
    {
        $criterias = $this->getUniqueCriteria();
        foreach ($criterias as $criteria) {
            if ($criteria["value"] == null) {
                return false;
            }
        }
        
        if ($criterias) {
            $result = $this->get($criterias);
            if (! empty($result)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove object(s) from persisten storage
     *
     * If no criteria is given, an attempt to remove by unique property will be made
     *
     * @param array $criterias
     *            Criterias to remove by
     *            
     * @return void
     * @throws RuntimeException if no criteria was was given
     */
    public function remove(array $criterias = null)
    {
        if ($criterias) {
            if (isset($criterias[0])) {
                $result = $this->getAdvanced($criterias);
            } else {
                $result = $this->getSimple($criterias);
            }
            $this->getStorage()->remove($this->getCollectionName(), $result);
        } else {
            if ($this->getUniqueCriteria()) {
                $this->getStorage()->remove($this->getCollectionName(), $this->getAdvanced($this->getUniqueCriteria()));
            } else {
                throw new RuntimeException('Can not remove without a criteria');
            }
        }
    }

    /**
     * Update object(s) in persisten storage
     *
     * If no cristeria is give, an attempt to update by unique property will be made
     *
     * @param array $criterias
     *            Criterias to update by
     *            
     * @return void
     * @throws RuntimeException if no criteria was was given
     */
    public function update(array $criterias = null)
    {
        if ($criterias) {
            if (isset($criterias[0])) {
                $result = $this->getAdvanced($criterias);
            } else {
                $result = $this->getsimple($criterias);
            }
            $this->getStorage()->update($this->getCollectionName(), $this->hydrate(), $result);
        } else {
            if ($this->getUniqueCriteria()) {
                $this->getStorage()->update($this->getCollectionName(), $this->hydrate(), $this->getAdvanced($this->getUniqueCriteria()));
            } else {
                throw new RuntimeException('Can not update without a criteria');
            }
        }
    }

    /**
     * Insert the current object state to persisten storage
     *
     * @return void
     */
    private function insert()
    {
        $this->getStorage()->insert($this->getCollectionName(), $this->hydrate());
    }

    /**
     * Get list of object matching criterias
     *
     * @return Result Result of matching objects
     */
    public function get()
    {
        return $this->getAll();
    }

    /**
     * Get list of object matching criterias
     *
     * @return Result Result of matching objects
     */
    public function getAll()
    {
        return new Result(get_class($this));
    }

    /**
     * Autoload function
     *
     * @param string $className
     *            Name of the class to load
     *            
     * @return boolean true if the class was loaded, otherwise false
     */
    public static function autoload($className)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return false;
        }
        $class = self::getPath() . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if (file_exists($class)) {
            require_once $class;
            return true;
        }
        
        $class = self::getPath() . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if (file_exists($class)) {
            require_once $class;
            return true;
        }
        $class = TickManager::getModelPath() . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (file_exists($class)) {
            require_once $class;
            return true;
        }
        return false;
    }

    /**
     * Get the root path to Tick
     *
     * @return string
     */
    public static function getPath()
    {
        if (! self::$path) {
            self::$path = dirname(__FILE__);
        }
        return self::$path;
    }
}