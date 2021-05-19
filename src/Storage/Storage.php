<?php

namespace Localgod\Tick\Storage;

/**
 * Tick storage interface
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Tick storage interface
 *
 * Interface for basic CRUD operation on storage.
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
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
    public function get(
        string $collection,
        array $fields,
        array $criterias,
        array $order = array(),
        bool $direction = true,
        int|null $limit = null,
        int|null $offset = null
    ): array;

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
    public function insert(string $collection, array $data): int;

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
    public function update(string $collection, array $data, array $criterias): void;

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
    public function remove(string $collection, array $criterias): void;

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
    public function exists(string $collection, array $criterias): bool;

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
    public function count(string $collection, array $criterias): int;

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
