<?php
/**
 * Tick sql storage implementation
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
 * Tick sql storage implementation
 *
 * All databases supported by PDO should work, but it has only been tested
 * with mysql and sqlite.
 *
 * @category ActiveRecord
 * @package	 Tick
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license	 http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-04-09
 */
class SqlStorage implements Storage {
	/**
	 * Database connection
	 *
	 * @var PDO
	 */
	private $_connection;

	/**
	 * Set the database connection
	 *
	 * @param PDO $connection Database connection
	 *
	 * @return void
	 */
	public function __construct(PDO $connection) {
		$this->_connection = $connection;
	}

	/**
	 * Get storage connection
	 *
	 * @return PDO A PDO instance
	 * @see Storage::getConnection()
	 */
	public function getConnection() {
		return $this->_connection;
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

	/**
	 * Get entities in storage
	 *
	 * @param string $collection Collection to search
	 * @param array  $fields     Properties to get
	 * @param array  $criterias  Criterias to search by
	 * @param array  $order		 Order result
	 * @param array  $direction  Order direction
	 * @param string $limit		 Limit result
	 * @param string $offset	 Offset result
	 *
	 * @return array Array with Associative arrays with fieldname=>value
	 * @see Storage::get()
	 */
	public function get($collection, array $fields, array $criterias, array $order = array(), $direction = true, $limit = '', $offset = '') {
		if (count($fields) > 0) {
			$select = "`" . implode("`,`", $fields) . "`";
		} else {
			$select = '*';
		}
		$sql = "SELECT $select FROM `" . $collection . "` " . $this->_prepareCriteria($criterias) . " " . $this->_orderBy($order, $direction) . " " . $this->_limit($limit, $offset) . ";";
		try {
			$statement = $this->_connection->prepare($sql);
			$statement->execute($this->_criteria($criterias));
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new RuntimeException('Query : "'.self::_interpolateQuery($sql, $this->_criteria($criterias)).'" returned error : '.$e->getMessage());
		}
	}

	/**
	 * Convert limit criteria to limit clause
	 *
	 * @param integer $limit  Limit
	 * @param integer $offset Offset
	 *
	 * @return string Sql representation of a limit clause
	 */
	private function _limit($limit, $offset) {
		if (!$offset == '') {
			return 'LIMIT '.$offset.','.$limit;
		} elseif (!$limit == '') {
			return 'LIMIT '.$limit;
		}
		return '';
	}

	/**
	 * Convert order criteria to order clause
	 *
	 * @param array   $order     Order
	 * @param boolean $direction Direction of the order (true = ascending, false descending)
	 *
	 * @return string Sql representation of a order clause
	 */
	private function _orderBy(array $order, $direction = true) {
		if (!empty($order)) {
			$orderString = array();
			$orderString[] = 'ORDER BY';
			for ($i = 0; count($order) > $i; $i++) {
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
	 * @param array $criterias List of criterias
	 *
	 * @return array values to fill where clause
	 */
	private function _criteria(array $criterias) {
		$where = array();
		if (!empty($criterias)) {
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
	 * @param array $criterias List of criterias
	 *
	 * @return string Sql representation of a where clause
	 */
	private function _prepareCriteria(array $criterias) {
		if (!empty($criterias)) {
			$where = array();
			$where[] = 'WHERE ';
			foreach ($criterias as $criteria) {
				if (sizeof($where) > 1) {
					$where[] = ' AND ';
				}
				$where[] = '`'.$criteria['property'] . '` ' . $criteria['condition'] . ' ?';
			}

			return implode('', $where);
		}
		return '';
	}

	/**
	 * Insert entity to storage
	 *
	 * @param string $collection Collection to insert into
	 * @param array  $data		 Associative array with fieldname => value
	 *
	 * @return integer Id of the object inserted
	 * @see Storage::insert()
	 */
	public function insert($collection, array $data) {
		$values = array();

		foreach ($data as $field => $value) {
			if ($value['type'] == 'DateTime') {
				$values[] = $this->_convertDateTime($value['value']);
				continue;
			}
			$values[] = $value['value'];
		}

		$sql = "INSERT INTO `" . $collection. "` (" . implode(', ', array_keys($data)) . ") VALUES (" . implode(', ', array_fill(0, count($data), '?')) . ");";

		try {
			$statement = $this->_connection->prepare($sql);
			$statement->execute($values);
			return $this->_connection->lastInsertId();
		} catch (PDOException $e) {
			throw new RuntimeException('Query : "'.self::_interpolateQuery($sql, $values).'" returned error : '.$e->getMessage());
		}
	}

	/**
	 * Convert DateTime object to a valid database representation
	 *
	 * @param DateTime $value Value to convert
	 *
	 * @return string Sql representation of a datetime value
	 */
	private static function _convertDateTime(DateTime $value = null) {
		if ($value instanceof DateTime) {
			return $value->format('Y-m-d H:i:s');
		}
		return "NULL";
	}

	/**
	 * Update entity in storage
	 *
	 * @param string $collection Collection to update
	 * @param array  $data		 Associative array with fieldname=>value
	 * @param array  $criterias  Criteria of the object to update
	 *
	 * @return void
	 * @see Storage::update()
	 */
	public function update($collection, array $data, array $criterias) {
		$setString = array();
		$values = array();
		foreach ($data as $field => $value) {

			$setString[] = '`' . $field . "` = ?";

			if ($value['type'] == 'DateTime') {
				$values[] = self::_convertDateTime($value['value']);
				continue;
			}
			$values[] = $value['value'];

		}
		$sql = "UPDATE `" . $collection . "` SET " . implode(', ', $setString) . " " . $this->_prepareCriteria($criterias) . ";";
		$values = array_merge($values, $this->_criteria($criterias));
		try {
			$statement = $this->_connection->prepare($sql);
			$statement->execute($values);
		} catch (PDOException $e) {
			throw new RuntimeException('Query : "'.self::_interpolateQuery($sql, $values).'" returned error : '.$e->getMessage());
		}
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
		$sql = "DELETE FROM `" . $collection . "` " . $this->_prepareCriteria($criterias) . ";";
		try {
			$statement = $this->_connection->prepare($sql);
			$statement->execute($this->_criteria($criterias));
		} catch (PDOException $e) {
			throw new RuntimeException('Query : "'.self::_interpolateQuery($sql, $this->_criteria($criterias)).'" returned error : '.$e->getMessage());
		}
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
		return count($this->get($collection, array(), $criterias)) > 0;
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
		return count($this->get($collection, array(), $criterias));
	}

	/**
	 * Replaces any parameter placeholders in a query with the value of that
	 * parameter. Useful for debugging. Assumes anonymous parameters from
	 * $params are are in the same order as specified in $query
	 *
	 * @param string $query  The sql query with parameter placeholders
	 * @param array  $params The array of substitution parameters
	 *
	 * @return string The interpolated query
	 */
	private static function _interpolateQuery($query, $params) {
		$keys = array();

		//build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}
		}

		$query = preg_replace($keys, $params, $query, 1, $count);
		return $query;
	}
}