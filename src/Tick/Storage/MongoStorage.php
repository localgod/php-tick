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
 * @link     http://code.google.com/p/php-tick/ php-tick
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
 * @link       http://code.google.com/p/php-tick/ php-tick
 * @since      2011-10-04
 */
class MongoStorage implements Storage
{
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
	public function __construct(MongoDB $connection)
	{
		$this->_connection = $connection;
	}

	/**
	 * Get storage connection
	 *
	 * @return Mongo A Mongo instance
	 * @see Storage::getConnection()
	 */
	public function getConnection()
	{
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
	 * @param array   $limit      Limit result
	 * @param array   $offset     Offset result
	 *
	 * @return array Array with Associative arrays with fieldname=>value
	 * @see Storage::get()
	 */
	public function get($collection, array $fields,array $criterias, array $order = array(), $direction = true, $limit = '', $offset = '')
	{
		$sql = "SELECT * FROM ".$collection." ".$this->_criteria($criterias)." ".$this->_orderBy($order)." ".$this->_limit($limit).";";
		try {
			$statement = $this->_connection->query($sql);
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage()."\n";
			echo 'Failed query: '.$sql."\n";
			exit();
		}
	}

	/**
	 * Convert limit criteria to limit clause
	 *
	 * @param array $limit Limit
	 *
	 * @return string Sql representation of a limit clause
	 */
	private function _limit($limit)
	{
		if (!empty($limit)) {
			if (isset($limit[0])) {
				$limitString = array();
				$limitString[] = 'LIMIT';
				$limitString[] = $limit[0];
				if (isset($limit[1])) {
					$limitString[] = ',';
					$limitString[] = $limit[1];
				}
				return implode(' ', $limitString);
			}
		}
		return '';
	}
	/**
	 * Convert order criteria to order clause
	 *
	 * @param array $order Order
	 *
	 * @return string Sql representation of a order clause
	 */
	private function _orderBy(array $order)
	{
		if (!empty($order)) {
			if (isset($order[0])) {
				$orderString = array();
				$orderString[] = 'ORDER BY';
				if (is_array($order[0])) {
					$orderString[] = explode(',', $order[0]);
				} else {
					$orderString[] = $order[0];
				}
				if (isset($order[1])) {
					if ($order[1]) {
						$orderString[] = 'ASC';
					} else {
						$orderString[] = 'DESC';
					}
				} else {
					$orderString[] = 'ASC';
				}
				return implode(' ', $orderString);
			}
		}
		return '';
	}

	/**
	 * Convert criteria to where clause
	 *
	 * @param array $criterias List of criterias
	 *
	 * @return string Sql representation of a where clause
	 */
	private function _criteria(array $criterias)
	{
		if (!empty($criterias)) {
			$where = array();
			$where[] = 'WHERE ';
			foreach ($criterias as $criteria) {
				if (sizeof($where) > 1) {
					$where[] = ' AND ';
				}
				$where[] = $criteria['property'].' '.$criteria['condition'].' '.$criteria['value'];
			}
			return implode('', $where);
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
	public function insert($collection, array $data)
	{
		$columnString = array();
		$setArray = array();
		foreach ($data as $field => $value) {
			if ($value['value'] == '') {
				continue;//we dont insert empty values
			}

			if ($value['type'] == 'float') {
				$setArray[$field] = $this->_convertFloat($value['value']);
				continue;
			}
			if ($value['type'] == 'integer') {
				$setArray[$field] = $this->_convertInteger($value['value']);
				continue;
			}
			if ($value['type'] == 'string') {
				$setArray[$field] = $this->_convertString($value['value']);
				continue;
			}
			if ($value['type'] == 'DateTime') {
				$setArray[$field] = $this->_convertDateTime($value['value']);
				continue;
			}
		}
		try {
			$mongoCollection = $this->_connection->selectCollection($collection);
			$cursor = $mongoCollection->find();
			foreach ($cursor as $doc) {
				var_dump($doc);
			}
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
	 * @return string Sql representation of a datetime value
	 */
	private static function _convertDateTime(DateTime $value = null)
	{
		if ($value instanceof DateTime) {
			return $value->format('U');
		}
		return "NULL";
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
	public function update($collection, array $data, array $criterias)
	{
		$setString = array();

		foreach ($data as $field => $value) {

			if ($value['value'] === null) {
				continue;
			}

			if ($value['type'] == 'integer') {
				$setString[] = $field." = ".$this->_convertInteger($value['value']);
				continue;
			}
			if ($value['type'] == 'float') {
				$setString[] = $field." = ".$this->_convertFloat($value['value']);
				continue;
			}
			if ($value['type'] == 'string') {

				$setString[] = $field." = ".$this->_convertString($value['value']);
				continue;
			}

			if ($value['type'] == 'DateTime') {
				$setString[] = $field." = ".$this->_convertDateTime($value['value']);
				continue;
			}
		}
		$sql = "UPDATE ".$collection."
		        SET ".implode(', ', $setString)." ".$this->_criteria($criterias).";";
		try {
			$statement = $this->_connection->query($sql);
		} catch (PDOException $e) {
			echo $e->getMessage()."\n";
			echo 'Failed query: '.$sql."\n";
			exit();
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
	public function remove($collection, array $criterias)
	{
		$sql = "DELETE FROM ".$collection." ".$this->_criteria($criterias).";";
		try {
			$statement = $this->_connection->query($sql);
		} catch (PDOException $e) {
			echo $e->getMessage()."\n";
			echo 'Failed query: '.$sql."\n";
			exit();
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
	public function exists($collection, array $criterias)
	{
		$sql = "SELECT COUNT(*) FROM ".$collection." ".$this->_criteria($criterias).";";

		try {
			if ($res = $this->_connection->query($sql)) {
				if ($res->fetchColumn() == 1) {
					return true;
				}
			}
		} catch (PDOException $e) {
			echo $e->getMessage()."\n";
			echo 'Failed query: '.$sql."\n";
			exit();
		}
		return false;
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
		$sql = "SELECT COUNT(*) FROM ".$collection." ".$this->_criteria($criterias).";";

		try {
			if ($res = $this->_connection->query($sql)) {
				if ($row = $res->fetchArray()) {
					return $row[0];
				}
			}
		} catch (PDOException $e) {
			echo $e->getMessage()."\n";
			echo 'Failed query: '.$sql."\n";
			exit();
		}
		return 0;
	}

	/**
	 * Convert integer to a valid database representation
	 *
	 * @param integer $value Value to convert
	 *
	 * @return integer|string Sql representation of integer value
	 */
	private function _convertInteger($value)
	{
		if ($value === null) {
			return "NULL";
		} elseif ($value === '') {
			return "''";
		}
		return $value;
	}
	/**
	 * Convert float to a valid database representation
	 *
	 * @param float $value Value to convert
	 *
	 * @return float|string Sql representation of float value
	 */
	private function _convertFloat($value)
	{
		if ($value === null) {
			return "NULL";
		} elseif ($value === '') {
			return "''";
		}
		return $value;
	}

	/**
	 * Close storage connection
	 *
	 * @see Storage::closeConnection()
	 */
	public function closeConnection() {
		$this->_connection = null;
		unset($this->_connection);
	}

	/**
	 * Convert string to a valid database representation
	 *
	 * @param mixed $value Value to convert
	 *
	 * @return string Sql representation of string value
	 */
	private function _convertString($value)
	{
		if ($value === null) {
			return "NULL";
		} elseif ($value === '') {
			return "''";
		}
		return "'".$value."'";
	}
}