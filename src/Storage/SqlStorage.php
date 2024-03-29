<?php

/**
 * Tick sql storage implementation
 *
 * PHP version >=8.0
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */

namespace Localgod\Tick\Storage;

use PDO;
use DateTime;
use PDOException;
use RuntimeException;

/**
 * Tick sql storage implementation
 *
 * All databases supported by PDO should work, but it has only been tested
 * with mysql and sqlite.
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class SqlStorage implements Storage
{
    /**
     * Database connection
     *
     * @var PDO
     */
    private PDO|null $connection;

    /**
     * Set the database connection
     *
     * @param PDO $connection
     *            Database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get storage connection
     *
     * @return PDO A PDO instance
     * @see Storage::getConnection()
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Close storage connection
     *
     * @return void
     * @see Storage::closeConnection()
     */
    public function closeConnection(): void
    {
        $this->connection = null;
        unset($this->connection);
    }

    /**
     * Get entities in storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $fields
     *            Properties to get
     * @param array $criterias
     *            Criterias to search by
     * @param array $order
     *            Order result
     * @param boolean $direction
     *            Order direction
     * @param string $limit
     *            Limit result
     * @param string $offset
     *            Offset result
     *
     * @return array Array with Associative arrays with fieldname=>value
     * @see Storage::get()
     */
    public function get(
        string $collection,
        array $fields,
        array $criterias,
        array $order = array(),
        bool $direction = true,
        int|null $limit = null,
        int|null $offset = null
    ): array {

        if (count($fields) > 0) {
            $select = "`" . implode("`,`", $fields) . "`";
        } else {
            $select = '*';
        }
        $sql = "SELECT $select 
                FROM `" . $collection . "` " .
                $this->prepareCriteria($criterias) . " " .
                $this->orderBy($order, $direction) . " " .
                $this->limit($limit, $offset) . ";";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($this->criteria($criterias));
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = 'Query : "' .
            self::interpolateQuery($sql, $this->criteria($criterias)) . '" returned error : ' . $e->getMessage();
            throw new RuntimeException($message);
        }
    }

    /**
     * Convert limit criteria to limit clause
     *
     * @param integer $limit
     *            Limit
     * @param integer $offset
     *            Offset
     *
     * @return string Sql representation of a limit clause
     */
    private function limit(int|null $limit, int|null $offset): string
    {
        if (! $offset == null) {
            return 'LIMIT ' . $offset . ',' . $limit;
        } elseif (! $limit == null) {
            return 'LIMIT ' . $limit;
        }
        return '';
    }

    /**
     * Convert order criteria to order clause
     *
     * @param array $order
     *            Order
     * @param boolean $direction
     *            Direction of the order (true = ascending, false descending)
     *
     * @return string Sql representation of a order clause
     */
    private function orderBy(array $order, bool $direction = true): string
    {
        if (! empty($order)) {
            $count = count($order);
            $orderString = array();
            $orderString[] = 'ORDER BY';
            for ($i = 0; $count > $i; $i++) {
                if ($i == 0) {
                    $orderString[] = '`' . $order[$i] . '`';
                } else {
                    $orderString[] = ', `' . $order[$i] . '`';
                }
            }
            $orderString[] = $direction ? 'ASC' : 'DESC';
            return implode(' ', $orderString);
        }
        return '';
    }

    /**
     * Get criteria values
     *
     * @param array $criterias
     *            List of criterias
     *
     * @return array values to fill where clause
     */
    private function criteria(array $criterias): array
    {
        $where = array();
        if (! empty($criterias)) {
            foreach ($criterias as $criteria) {
                if ($criteria['value'] instanceof DateTime) {
                    $where[] = $criteria['value']->format(DateTime::ISO8601);
                } else {
                    $where[] = $criteria['value'];
                }
            }
        }
        return $where;
    }

    /**
     * Convert criteria to where clause
     *
     * @param array $criterias
     *            List of criterias
     *
     * @return string Sql representation of a where clause
     */
    private function prepareCriteria(array $criterias): string
    {
        if (! empty($criterias)) {
            $where = array();
            $where[] = 'WHERE ';
            foreach ($criterias as $criteria) {
                if (sizeof($where) > 1) {
                    $where[] = ' AND ';
                }
                $where[] = '`' . $criteria['property'] . '` ' . $criteria['condition'] . ' ?';
            }

            return implode('', $where);
        }
        return '';
    }

    /**
     * Insert entity to storage
     *
     * @param string $collection
     *            Collection to insert into
     * @param array $data
     *            Associative array with fieldname => value
     *
     * @return integer Id of the object inserted
     * @see Storage::insert()
     */
    public function insert(string $collection, array $data): int
    {
        $values = array();

        foreach ($data as $field => $value) {
            if ($value['type'] == 'DateTime') {
                $values[] = $this->convertDateTime($value['value']);
                continue;
            }
            $values[] = $value['value'];
        }

        $sql = "INSERT INTO `" . $collection . "` (" . implode(', ', array_keys($data)) . ") 
                VALUES (" . implode(', ', array_fill(0, count($data), '?')) . ");";

        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($values);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $message = 'Query : "' . self::interpolateQuery($sql, $values) . '" returned error : ' . $e->getMessage();
            throw new RuntimeException($message);
        }
    }

    /**
     * Convert DateTime object to a valid database representation
     *
     * @param DateTime $value
     *            Value to convert
     *
     * @return string Sql representation of a datetime value
     */
    private static function convertDateTime(DateTime $value = null): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return "NULL";
    }

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
     * @see Storage::update()
     */
    public function update(string $collection, array $data, array $criterias): void
    {
        $setString = array();
        $values = array();
        foreach ($data as $field => $value) {
            $setString[] = '`' . $field . "` = ?";

            if ($value['type'] == 'DateTime') {
                $values[] = self::convertDateTime($value['value']);
                continue;
            }
            $values[] = $value['value'];
        }
        $sql = "UPDATE `" . $collection . "` 
                SET " . implode(', ', $setString) . " " . $this->prepareCriteria($criterias) . ";";
        $values = array_merge($values, $this->criteria($criterias));
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($values);
        } catch (PDOException $e) {
            $message = 'Query : "' . self::interpolateQuery($sql, $values) . '" returned error : ' . $e->getMessage();
            throw new RuntimeException($message);
        }
    }

    /**
     * Remove entity from storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to remove
     *
     * @return void
     * @see Storage::remove()
     */
    public function remove(string $collection, array $criterias): void
    {
        $sql = "DELETE FROM `" . $collection . "` " . $this->prepareCriteria($criterias) . ";";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($this->criteria($criterias));
        } catch (PDOException $e) {
            $message = 'Query : "' .
            self::interpolateQuery($sql, $this->criteria($criterias)) . '" returned error : ' . $e->getMessage();
            throw new RuntimeException($message);
        }
    }

    /**
     * Entity exists in storage
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to check for
     *
     * @return boolean if the entity exists
     * @see Storage::exists()
     */
    public function exists(string $collection, array $criterias): bool
    {
        return count($this->get($collection, array(), $criterias)) > 0;
    }

    /**
     * Count number of entities matching the criteria
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria of the object to check for
     *
     * @return integer
     * @see Storage::count()
     */
    public function count(string $collection, array $criterias): int
    {
        return count($this->get($collection, array(), $criterias));
    }

    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter.
     * Useful for debugging. Assumes anonymous parameters from
     * $params are are in the same order as specified in $query
     *
     * @param string $query
     *            The sql query with parameter placeholders
     * @param array $params
     *            The array of substitution parameters
     *
     * @return string The interpolated query
     */
    private static function interpolateQuery(string $query, array $params): string
    {
        $keys = array();

        // build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }
        }

        $query = preg_replace($keys, $params, $query, 1);
        return $query;
    }
}
