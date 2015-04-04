<?php

/**
 * Tick Manager
 *
 * PHP version 5.2
 *
 * @category ActiveRecord
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
/**
 * Tick Manager
 *
 * Manages storage connection and autoloading of models.
 *
 * @category ActiveRecord
 * @package Tick
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class TickManager
{

    /**
     * Default connection name
     *
     * @var string
     */
    const DEFAULT_CONNECTION_NAME = 'default';

    /**
     * Path to Tick model directory
     *
     * @var string $path Tick root directory
     */
    private static $modelPath;

    /**
     * List of all defined connections by name
     *
     * @var array
     */
    private static $connections = array();

    /**
     * Get storage instance
     *
     * @param string $connectionName
     *            Name of connection
     *            
     * @return Storage
     * @throws RuntimeException if the storage could not be retrived
     */
    public static function getStorage($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        if (! key_exists($connectionName, self::$connections)) {
            throw new InvalidArgumentException("No connection named '" . $connectionName . "' has been configured");
        }
        
        $connection = self::$connections[$connectionName];
        $unique_name = self::getUniqueName($connectionName);
        
        if (! key_exists($unique_name, $GLOBALS)) {
            $GLOBALS[$unique_name] = null;
        }
        if (! $GLOBALS[$unique_name] instanceof Storage) {
            if ($connection['type'] == 'mongodb') {
                self::createMongoStorage($connectionName);
            } else {
                if ($connection['type'] == 'solr') {
                    self::createSolrStorage($connectionName);
                } else {
                    self::createSqlStorage($connectionName);
                }
            }
        }
        return $GLOBALS[$unique_name];
    }

    /**
     * Get name of current database
     *
     * @param string $connectionName
     *            Name of connection
     *            
     * @return string
     */
    public static function getDatabaseName($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        return self::$connections[$connectionName]['database'];
    }

    /**
     * Create sql storage
     *
     * @param string $connectionName
     *            Name of connection
     *            
     * @throws RuntimeException if connection creation failed
     *        
     * @return void
     */
    private static function createSqlStorage($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        $connection = self::$connections[$connectionName];
        $unique_name = self::getUniqueName($connectionName);
        
        $dsn = $connection['type'] . ':host=' . $connection['host'] . ';dbname=' . $connection['database'];
        $connection['port'] != null ? $dsn = $dsn . ';port=' . $connection['port'] : null;
        
        if ($connection['type'] == 'sqlite' && (file_exists($connection['database']) || $connection['database'] == ':memory:')) {
            $dsn = $connection['type'] . ':' . $connection['database'];
        }
        try {
            $pdo = new PDO($dsn, $connection['username'], $connection['password'], $connection['driverOptions']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $GLOBALS[$unique_name] = new SqlStorage($pdo);
        } catch (PDOException $e) {
            throw new RuntimeException('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Create mongo storage
     *
     * @param string $connectionName
     *            name of connection
     *            
     * @throws RuntimeException if connection creation failed
     *        
     * @return void
     */
    private static function createMongoStorage($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        $connection = self::$connections[$connectionName];
        $unique_name = self::getUniqueName($connectionName);
        
        $dsn = $connection['type'] . '://' . $connection['host'];
        $connection['port'] != null ? $dsn = $dsn . ':' . $connection['port'] : null;
        try {
            if (is_array($connection['driverOptions'])) {
                $mongo = new Mongo($dsn, $connection['driverOptions']);
            } else {
                $mongo = new Mongo($dsn);
            }
            
            $mongo->connect();
            $mongoDb = $mongo->selectDB($connection['database']);
            $GLOBALS[$unique_name] = new MongoStorage($mongoDb);
        } catch (MongoConnnectionException $e) {
            throw new RuntimeException('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Create SOLR storage
     *
     * @param string $connectionName
     *            name of connection
     *            
     * @throws RuntimeException if connection creation failed
     *        
     * @return void
     */
    private static function createSolrStorage($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        $connection = self::$connections[$connectionName];
        $unique_name = self::getUniqueName($connectionName);
        
        $options = array();
        
        if ($connection['host']) {
            $options['hostname'] = $connection['host'];
        }
        if ($connection['username']) {
            $options['login'] = $connection['username'];
        }
        if ($connection['password']) {
            $options['password'] = $connection['password'];
        }
        if ($connection['port']) {
            $options['port'] = $connection['port'];
        }
        
        $client = new SolrClient($options);
        $GLOBALS[$unique_name] = new SolrStorage($client);
    }

    /**
     * Get model path
     *
     * @return string
     */
    public static function getModelPath()
    {
        return self::$modelPath;
    }

    /**
     * Set model path
     *
     * @param string $path
     *            Math to models
     *            
     * @throws InvalidArgumentException on non existing path
     * @return void
     */
    public static function setModelPath($path)
    {
        if (! file_exists($path)) {
            throw new InvalidArgumentException('Model path could not be found:' . $path);
        }
        self::$modelPath = $path;
    }

    /**
     * Set the default database connection
     *
     * @param string $type
     *            Pdo supported sql databases or mongodb
     * @param string $database
     *            The database name
     * @param string $username
     *            Username
     * @param string $password
     *            Password
     * @param string $host
     *            The host name of the data source
     * @param integer $port
     *            The the port of the data source
     * @param array $driver_options
     *            Driver options
     *            
     * @throws InvalidArgumentException missing database driver or database name
     * @return void
     */
    public final static function addDefaultConnectionConfig($type, $database, $username = null, $password = null, $host = '127.0.0.1', $port = null, array $driver_options = null)
    {
        self::addConnectionConfig(self::DEFAULT_CONNECTION_NAME, $type, $database, $username, $password, $host, $port, $driver_options);
    }

    /**
     * Set the database connection
     *
     * @param string $name
     *            Connection name for later retrieval
     * @param string $type
     *            Pdo supported sql databases or mongodb
     * @param string $database
     *            The database name
     * @param string $username
     *            Username
     * @param string $password
     *            Password
     * @param string $host
     *            The host name of the data source
     * @param integer $port
     *            The the port of the data source
     * @param array $driver_options
     *            Driver options
     *            
     * @throws InvalidArgumentException missing database driver or database name
     * @return void
     */
    public final static function addConnectionConfig($name, $type, $database, $username = null, $password = null, $host = '127.0.0.1', $port = null, array $driver_options = null)
    {
        $drivers = PDO::getAvailableDrivers();
        $drivers[] = 'mongodb';
        $drivers[] = 'solr';
        
        if (! in_array($type, $drivers)) {
            throw new InvalidArgumentException('Only pdo supported sql databases, solr and mongo is supported at the moment.(' . $type . ')');
        }
        
        if (empty($database)) {
            throw new InvalidArgumentException('No database specified');
        }
        
        $connection = array();
        $connection["type"] = $type;
        $connection["database"] = $database;
        $connection["username"] = $username;
        $connection["password"] = $password;
        $connection["host"] = $host;
        $connection["port"] = $port;
        $connection["driverOptions"] = $driver_options;
        self::$connections[$name] = $connection;
    }

    /**
     * Closes and emoves all connections
     *
     * @return void
     */
    public static function removeAllConnections()
    {
        foreach (self::$connections as $name => $obj) {
            self::removeConnectionConfig($name);
        }
    }

    /**
     * Closes and removes the a connection
     *
     * @param string $connectionName
     *            Connection name
     *            
     * @return void
     */
    public static function removeConnectionConfig($connectionName = self::DEFAULT_CONNECTION_NAME)
    {
        if (self::$connections[$connectionName]) {
            unset(self::$connections[$connectionName]);
        }
        
        $unique_name = self::getUniqueName($connectionName);
        if (key_exists($unique_name, $GLOBALS)) {
            $GLOBALS[$unique_name]->closeConnection();
            unset($GLOBALS[$unique_name]);
        }
    }

    /**
     * The unigue name of connection in $GLOBALS
     *
     * @param string $connectionName
     *            Connection name
     *            
     * @return string unique name
     */
    protected static function getUniqueName($connectionName)
    {
        return "TickConnection:" . $connectionName;
    }
}