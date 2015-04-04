<?php

/**
 * Tick SOLR storage
 *
 * You should use a schema like the schema next to this file, solr-schema.xml
 * This requires your models to have an id property of type string.
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Tick SOLR storage
 *
 * Basic CRUD operations on SOLR based active records.
 *
 * @category ActiveRecord
 * @package Tick
 * @subpackage Storage
 * @author Jens Riisom Schultz <jers@fynskemedier.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class SolrStorage implements Storage
{

    /**
     * Database connection
     *
     * @var SolrClient
     */
    private $connection;

    /**
     * Set the database connection
     *
     * @param SolrClient $connection
     *            Database connection
     *            
     * @return void
     */
    public function __construct(SolrClient $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get storage connection
     *
     * @return SolrClient A SolrClient instance
     * @see Storage::getConnection()
     */
    public function getConnection()
    {
        return $this->connection;
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
     */
    public function get($collection, array $fields, array $criterias, array $order = array(), $direction = true, $limit = null, $offset = 0)
    {
        $query = $this->getQuery($collection, $criterias);
        
        foreach ($fields as $field) {
            $query->addField($field);
        }
        
        $query->setStart($offset);
        if ($limit !== null) {
            $query->setRows($limit);
        }
        
        foreach ($order as $field) {
            $query->addSortField($field, $direction ? SolrQuery::ORDER_DESC : SolrQuery::ORDER_ASC);
        }
        
        $response = $this->connection->query($query)->getResponse()->response;
        
        $result = array();
        if ($response->numFound > 0) {
            foreach ($response->docs as $doc) {
                $subResult = array();
                foreach ($fields as $field) {
                    $subResult[$field] = $doc->$field;
                }
                $result[] = $subResult;
            }
        }
        
        return $result;
    }

    /**
     * Insert entity to storage
     *
     * @param string $collection
     *            Collection to insert into
     * @param array $data
     *            Associative array with fieldname=>[value=>value,type=>type]
     *            
     * @return integer Id of the object inserted
     *         @trows SolrClientException|Exception
     */
    public function insert($collection, array $data)
    {
        $doc = new SolrInputDocument();
        $doc->addField('collection', $collection);
        
        foreach ($data as $key => $d) {
            if ($key != 'id') {
                switch ($d['type']) {
                    case 'float':
                        $doc->addField($key, (float) $d['value']);
                        break;
                    case 'double':
                        $doc->addField($key, (double) $d['value']);
                        break;
                    case 'integer':
                        $doc->addField($key, (integer) $d['value']);
                        break;
                    case 'string':
                        $doc->addField($key, $d['value']);
                        break;
                    case 'array':
                        throw new Exception("array is not supported yet.");
                        break;
                    case 'DateTime':
                        $doc->addField($key, $d['value'] ? $d['value']->getTimestamp() : '');
                        break;
                }
            }
        }
        
        $this->connection->addDocument($doc);
        $this->connection->commit();
    }

    /**
     * Update entity in storage
     *
     * @param string $collection
     *            Collection to update
     * @param array $data
     *            Associative array with fieldname=>[value=>value,type=>type]
     * @param array $criterias
     *            Criteria of the object to update
     *            
     * @return void
     */
    public function update($collection, array $data, array $criterias)
    {
        $this->remove($collection, $criterias);
        $this->insert($collection, $data);
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
     */
    public function remove($collection, array $criterias)
    {
        $query = $this->getQuery($collection, $criterias);
        $query->addField('id');
        
        $response = $this->connection->query($query)->getResponse()->response;
        
        $ids = array();
        foreach ($response->docs as $doc) {
            $ids[] = $doc['id'];
        }
        
        $this->connection->deleteByIds($ids);
        
        $this->connection->commit();
    }

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
    public function exists($collection, array $criterias)
    {
        $query = $this->getQuery($collection, $criterias);
        $query->addField('id');
        
        $response = $this->connection->query($query)->getResponse()->response;
        
        return $response->numFound > 0;
    }

    /**
     * Count the number of entities matching the given criteria
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria
     *            
     * @return integer
     */
    public function count($collection, array $criterias)
    {
        $query = $this->getQuery($collection, $criterias);
        $query->setRows(0);
        
        $response = $this->connection->query($query)->getResponse()->response;
        
        return $response->numFound;
    }

    /**
     * Get a SOLR query with the given criterias, on the given collection.
     *
     * @param string $collection
     *            Collection to search
     * @param array $criterias
     *            Criteria
     *            
     * @return SolrQuery
     */
    private function getQuery($collection, $criterias)
    {
        $query = new SolrQuery();
        
        $queryParts = array();
        $queryParts[] = "collection:\"$collection\"";
        
        foreach ($criterias as $crit) {
            switch ($crit['condition']) {
                case '>=':
                    $queryParts[] = $crit['property'] . ':[' . $crit['value'] . ' TO *]';
                    break;
                case '<=':
                    $queryParts[] = $crit['property'] . ':[* TO ' . $crit['value'] . ']';
                    break;
                case '=':
                    $queryParts[] = $crit['property'] . ':"' . $crit['value'] . '"';
                    break;
                case 'MATCHES':
                    $queryParts[] = $crit['value'];
                    break;
                default:
                    throw new Exception("The {$crit['condition']} operator is not supported.");
            }
        }
        
        $query->setQuery(implode(' AND ', $queryParts));
        
        return $query;
    }
}