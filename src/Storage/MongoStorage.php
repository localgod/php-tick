<?php
namespace Localgod\Tick\Storage;

/**
 * Tick mongo storage implementation
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
 use \MongoDB;
 use \MongoId;
 use \MongoRegex;
 use \MongoConnectionException;
 use \MongoCursorTimeoutException;
 use \MongoDate;
 use \Exception;
 use \RuntimeException;
 use \DateTime;

/**
 * Tick mongo storage implementation
 *
 * MongoDB (from "humongous") is a scalable, high-performance, open source,
 * document-oriented database.
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class MongoStorage implements Storage
{

    /**
     * Database connection
     *
     * @var MongoDB
     */
    private $connection;

    /**
     * Set the database connection
     *
     * @param MongoDB $connection
     *            Database connection
     */
    public function __construct(MongoDB $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get storage connection
     *
     * @return Mongo A Mongo instance
     * @see Storage::getConnection()
     */
    public function getConnection()
    {
        return $this->connection;
    }

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
     * @param integer $limit
     *            Limit result
     * @param integer $offset
     *            Offset result
     *
     * @return array Array with Associative arrays with fieldname=>value
     * @see Storage::get()
     * @throws RuntimeException if the query failed
     */
    public function get(
        $collection,
        array $fields,
        array $criterias,
        array $order = array(),
        $direction = true,
        $limit = null,
        $offset = null
    ) {
    
        $mongoCollection = $this->connection->selectCollection($collection);
        
        $cursor = $mongoCollection->find($this->criteria($criterias), $fields);
        
        // This is a amputated way of handeling it.....
        // we should support multiple ordering clauses.
        if (isset($order[0])) {
            $cursor->sort(array(
                $order[0] => ($direction ? 1 : - 1)
            ));
        }
        
        if ($offset !== null) {
            $cursor->skip($offset);
        }
        if ($limit !== null) {
            $cursor->limit($limit);
        }
        
        $result = array();
        try {
            while ($entry = $cursor->getNext()) {
                $row = array();
                foreach ($entry as $key => $value) {
                    if ($value instanceof MongoId) {
                        $row[$key] = $value->__tostring();
                    } elseif ($value instanceof MongoDate) {
                        $row[$key] = date('Y-m-d h:i:s', $value->sec);
                    } else {
                        $row[$key] = $value;
                    }
                }
                $result[] = $row;
            }
            return $result;
        } catch (MongoConnectionException $e) {
            throw new RuntimeException('Query : returned error : ' . $e->getMessage());
        } catch (MongoCursorTimeoutException $e) {
            throw new RuntimeException('Query : returned error : ' . $e->getMessage());
        }
    }

    /**
     * Convert criteria to search arguments
     *
     * @param array $criterias
     *            List of criterias
     *
     * @return array
     */
    private function criteria(array $criterias)
    {
        if (! empty($criterias)) {
            $where = array();
            $map = array(
                '<' => '$lt',
                '>' => '$gt',
                '<=' => '$lte',
                '>=' => '$gte'
            );
            
            foreach ($criterias as $criteria) {
                if ($criteria['property'] == '_id') {
                    $value = new MongoId($criteria['value']);
                } else {
                    $value = $criteria['value'];
                }
                if (array_key_exists($criteria['condition'], $map)) {
                    $where[$criteria['property']] = array(
                        '' . $map[$criteria['condition']] . '' => $value
                    );
                } elseif (preg_match('/^like$/i', $criteria['condition'])) {
                    $regxp = '/' . (substr($value, 0, 1) == '%' ? '' : '^') .
                    str_replace('%', '', $value) . (substr($value, - 1) == '%' ? '' : '$') . '/i';
                    $where[$criteria['property']] = new MongoRegex($regxp);
                } elseif ($criteria['condition'] == '=') {
                    $where[$criteria['property']] = $value;
                }
            }
            return $where;
        }
        return array();
    }

    /**
     * Insert entity to storage
     *
     * @param string $collection
     *            Collection to insert into
     * @param array $data
     *            Associative array with fieldname=>value
     *
     * @return integer Id of the object inserted
     * @see Storage::insert()
     */
    public function insert($collection, array $data)
    {
        $setArray = array();
        foreach ($data as $field => $value) {
            if ($value['value'] == '') {
                continue; // we dont insert empty values
            }
            
            if ($value['type'] == 'float') {
                $setArray[$field] = (float) $value['value'];
                continue;
            }
            if ($value['type'] == 'integer') {
                $setArray[$field] = (integer) $value['value'];
                continue;
            }
            if ($value['type'] == 'string') {
                $setArray[$field] = (string) $value['value'];
                continue;
            }
            if ($value['type'] == 'DateTime') {
                $setArray[$field] = self::convertDateTime($value['value']);
                continue;
            }
        }
        try {
            $mongoCollection = $this->connection->selectCollection($collection);
            $mongoCollection->insert($setArray);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo 'Failed insert in collection > $collection: ' . implode(', ', $setArray) . "\n";
        }
    }

    /**
     * Convert DateTime object to a valid database representation
     *
     * @param DateTime $value
     *            Value to convert
     *
     * @return MongoDate null representation of a datetime value
     */
    private static function convertDateTime(DateTime $value = null)
    {
        if ($value instanceof DateTime) {
            return new MongoDate(strtotime($value->format('Y-m-d h:i:s')));
        }
        return null;
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
    public function update($collection, array $data, array $criterias)
    {
        $setArray = array();
        foreach ($data as $field => $value) {
            if ($field == '_id') {
                continue; // we don't update the id
            }
            if ($value['value'] == '') {
                continue; // we don't insert empty values
            }
            
            if ($value['type'] == 'float') {
                $setArray[$field] = (float) $value['value'];
                continue;
            }
            if ($value['type'] == 'integer') {
                $setArray[$field] = (integer) $value['value'];
                continue;
            }
            if ($value['type'] == 'string') {
                $setArray[$field] = (string) $value['value'];
                continue;
            }
            if ($value['type'] == 'DateTime') {
                $setArray[$field] = self::convertDateTime($value['value']);
                continue;
            }
        }
        $mongoCollection = $this->connection->selectCollection($collection);
        $mongoCollection->update($this->criteria($criterias), array(
            '$set' => $setArray
        ));
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
    public function remove($collection, array $criterias)
    {
        $mongoCollection = $this->connection->selectCollection($collection);
        $mongoCollection->remove($this->criteria($criterias));
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
    public function exists($collection, array $criterias)
    {
        $mongoCollection = $this->connection->selectCollection($collection);
        $cursor = $mongoCollection->find($this->criteria($criterias));
        return $cursor->hasNext();
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
    public function count($collection, array $criterias)
    {
        $mongoCollection = $this->connection->selectCollection($collection);
        $cursor = $mongoCollection->find($this->criteria($criterias));
        return $cursor->count();
    }

    /**
     * Close storage connection
     *
     * @return void
     * @see Storage::closeConnection()
     */
    public function closeConnection()
    {
        $this->connection = null;
        unset($this->connection);
    }
}
