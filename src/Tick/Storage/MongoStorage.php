<?php
/**
 * Tick mongo storage implementation
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @package  Tick
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 * @since    2011-10-04
 */
/**
 * Tick mongo storage implementation
 *
 * MongoDB (from "humongous") is a scalable, high-performance, open source,
 * document-oriented database.
 *
 * @category   ActiveRecord
 * @package    Tick
 * @subpackage Storage
 * @author     Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       https://github.com/localgod/php-tick php-tick
 * @since      2011-10-04
 */
class MongoStorage implements Storage {
	
	/**
	 * Database connection
	 *
	 * @var MongoDB
	 */
	private $_connection;

	/**
	 * Set the database connection
	 *
	 * @param PDO $connection Database connection
	 *
	 * @return void
	 */
	public function __construct(MongoDB $connection) {
		$this->_connection = $connection;
	}

	/**
	 * Get storage connection
	 *
	 * @return Mongo A Mongo instance
	 * @see Storage::getConnection()
	 */
	public function getConnection() {
		return $this->_connection;
	}

	/**
	 * Get entities in storage
	 *
	 * @param string  $collection Collection to search
	 * @param array   $fields     Properties to fetch
	 * @param array   $criterias  Criterias to search by
	 * @param array   $order      Order result
	 * @param boolean $direction  Order direction
	 * @param string  $limit      Limit result
	 * @param string  $offset     Offset result
	 *
	 * @return array Array with Associative arrays with fieldname=>value
	 * @see Storage::get()
	 * @throws RuntimeException if the query failed
	 */
	public function get($collection, array $fields,array $criterias, array $order = array(), $direction = true, $limit = '', $offset = '') {
			$mongoCollection = $this->_connection->selectCollection($collection);
			$cursor = $mongoCollection->find($this->_criteria($criterias), $fields);
			$cursor->sort($order)->skip($offset)->limit($limit);
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
				throw new RuntimeException('Query : returned error : '.$e->getMessage());
			} catch (MongoCursorTimeoutException $e) {
				throw new RuntimeException('Query : returned error : '.$e->getMessage());
			}
	}

	/**
	 * Convert criteria to where clause
	 *
	 * @param array $criterias List of criterias
	 *
	 * @return string Sql representation of a where clause
	 */
	private function _criteria(array $criterias) {
		if (!empty($criterias)) {
			$where = array();
				
			foreach ($criterias as $criteria) {
				if ($criteria['condition'] == '=') {
					if ($criteria['property'] == '_id') {
						$theObjId = new MongoId($criteria['value']);
						$where[$criteria['property']] = $theObjId;
					} else {
						$where[$criteria['property']] = $criteria['value'];
					}
				} else {
					echo 'we only handle = at the moment';
					ob_flush();
				}
			}
			return $where;
		}
		return '';
	}

	/**
	 * Insert entity to storage
	 *
	 * @param string $collection Collection to insert into
	 * @param array  $data       Associative array with fieldname=>value
	 *
	 * @return integer Id of the object inserted
	 * @see Storage::insert()
	 */
	public function insert($collection, array $data) {
		$setArray = array();
		foreach ($data as $field => $value) {
			if ($value['value'] == '') {
				continue;//we dont insert empty values
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
				$setArray[$field] = self::_convertDateTime($value['value']);
				continue;
			}
		}
		try {
			$mongoCollection = $this->_connection->selectCollection($collection);
			$mongoCollection->insert($setArray);
		} catch (Exception $e) {
			echo $e->getMessage()."\n";
			echo 'Failed insert in collection > $collection: '.implode(', ', $setArray)."\n";
		}
	}
	
	/**
	 * Convert DateTime object to a valid database representation
	 *
	 * @param DateTime $value Value to convert
	 *
	 * @return MongoDate|null Mongo representation of a datetime value
	 */
	private static function _convertDateTime(DateTime $value = null) {
		if ($value instanceof DateTime) {
			return new MongoDate(strtotime($value->format('Y-m-d h:i:s')));
		}
		return null;
	}
	
	/**
	 * Update entity in storage
	 *
	 * @param string $collection Collection to update
	 * @param array  $data       Associative array with fieldname=>value
	 * @param array  $criterias  Criteria of the object to update
	 *
	 * @return void
	 * @see Storage::update()
	 */
	public function update($collection, array $data, array $criterias) {
		$setArray = array();
		foreach ($data as $field => $value) {
			if ($field == '_id') {
				continue;//we don't update the id
			}
			if ($value['value'] == '') {
				continue;//we don't insert empty values
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
				$setArray[$field] = new MongoDate(strtotime($value['value']->format('Y-m-d h:i:s')));
				continue;
			}
		}
		$mongoCollection = $this->_connection->selectCollection($collection);
		$mongoCollection->update($this->_criteria($criterias), array('$set' => $setArray));
	}
	
	/**
	 * Remove entity from storage
	 *
	 * @param string $collection Collection to search
	 * @param array  $criterias  Criteria of the object to remove
	 *
	 * @return void
	 * @see Storage::remove()
	 */
	public function remove($collection, array $criterias) {
		$mongoCollection = $this->_connection->selectCollection($collection);
		$mongoCollection->remove($this->_criteria($criterias));
	}
	
	/**
	 * Entity exists in storage
	 *
	 * @param string $collection Collection to search
	 * @param array  $criterias  Criteria of the object to check for
	 *
	 * @return boolean if the entity exists
	 * @see Storage::exists()
	 */
	public function exists($collection, array $criterias) {
		$mongoCollection = $this->_connection->selectCollection($collection);
		$cursor = $mongoCollection->find($this->_criteria($criterias));
		return $cursor->hasNext();
	}

	/**
	 * Count number of entities matching the criteria
	 *
	 * @param string $collection Collection to search
	 * @param array  $criterias  Criteria of the object to check for
	 *
	 * @return integer
	 * @see Storage::count()
	 */
	public function count($collection, array $criterias) {
		$mongoCollection = $this->_connection->selectCollection($collection);
		$cursor = $mongoCollection->find($this->_criteria($criterias));
		return $cursor->count();
	}

	/**
	 * Close storage connection
	 *
	 * @return void
	 * @see Storage::closeConnection()
	 */
	public function closeConnection() {
		$this->_connection = null;
		unset($this->_connection);
	}
}