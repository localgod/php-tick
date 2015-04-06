<?php
namespace Localgod\Tick\Storage;
/**
 * Tick storage interface
 *
 * PHP version >=5.3.3
 *
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Tick storage interface
 *
 * Interface for basic CRUD operation on storage.
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
interface Storage
{

    /**
     * Get entities in storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $fields
     *            Properties to fetch
     * @param array $criterias
     *            Criterias to search by
     * @param array $order
     *            Order result
     * @param boolean $direction
     *            Order direction
     * @param array $limit
     *            Limit result
     * @param array $offset
     *            Offset result
     *            
     * @return array Array with Associative arrays with fieldname=>value
     */
    public function get($collection, array $fields, array $criterias, array $order = array(), $direction = true, $limit = '', $offset = '');

    /**
     * Insert entity to storage
     *
     * @param string $collection
     *            Collection to insert into
     * @param array $data
     *            Associative array with fieldname=>value
     *            
     * @return integer Id of the object inserted
     */
    public function insert($collection, array $data);

    /**
     * Update entity in storage
     *
     * @param string $collection
     *            Collection to update
     * @param array $data
     *            Associative array with fieldname=>value
     * @param array $criterias
     *            Criteria of the object to update
     *            
     * @return void
     */
    public function update($collection, array $data, array $criterias);

    /**
     * Remove entity from storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to remove
     *            
     * @return void
     */
    public function remove($collection, array $criterias);

    /**
     * Entity exists in storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to check for
     *            
     * @return boolean
     */
    public function exists($collection, array $criterias);

    /**
     * Count the number of entities matching the given criteria
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to check for
     *            
     * @return integer
     */
    public function count($collection, array $criterias);

    /**
     * Close storage connection
     *
     * @return mixed
     */
    public function closeConnection();

    /**
     * Get storage connection
     *
     * @return mixed
     */
    public function getConnection();
}