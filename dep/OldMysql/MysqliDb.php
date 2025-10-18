<?php
/**
 * Command like Metatag writer for video files.
 */

namespace UTM\Bundle\mysql;

use UTM\Bundle\mysql\traits\MySQLDbGet;
use UTM\Bundle\mysql\traits\MySQLDbPrivate;
use UTM\Bundle\mysql\traits\MySQLDbProtected;
use UTM\Bundle\mysql\traits\MySQLDbSet;
use UTM\Bundle\mysql\traits\MySQLDbTransaction;
use UTM\Bundle\mysql\traits\MySQLLock;

/**
 * MysqliDb Class.
 *
 * @category  Database Access
 *
 * @author    Jeffery Way <jeffrey@jeffrey-way.com>
 * @author    Josh Campbell <jcampbell@ajillion.com>
 * @author    Alexander V. Butenko <a.butenka@gmail.com>
 * @copyright Copyright (c) 2010-2017
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 *
 * @see      http://github.com/joshcam/PHP-MySQLi-Database-Class
 *
 * @version   2.9.3
 */
class MysqliDb
{
    use MySQLDbPrivate;
    use MySQLDbProtected;
    use MySQLDbGet;
    use MySQLDbSet;
use MySQLDbTransaction;
use MySQLLock;

    /**
     * Table prefix.
     *
     * @var string
     */
    public static $prefix = '';

    /**
     * Variable which holds an amount of returned rows during get/getOne/select queries.
     *
     * @var string
     */
    public $count = 0;

    /**
     * Variable which holds an amount of returned rows during get/getOne/select queries with withTotalCount().
     *
     * @var string
     */
    public $totalCount = 0;

    /**
     * Return type: 'array' to return results as array, 'object' as object
     * 'json' as json string.
     *
     * @var string
     */
    public $returnType = 'array';
    public $trace = [];

    /**
     * Per page limit for pagination.
     *
     * @var int
     */
    public $pageLimit = 20;

    /**
     * Variable that holds total pages count of last paginate() query.
     *
     * @var int
     */
    public $totalPages = 0;

    /**
     * @var string the name of a default (main) mysqli connection
     */
    public $defConnectionName = 'default';

    public $autoReconnect = true;

    public $db_name = '';

    /**
     * Static instance of self.
     *
     * @var MysqliDb
     */
    protected static $_instance;

    /**
     * MySQLi instances.
     *
     * @var mysqli[]
     */
    protected $_mysqli = [];

    /**
     * The SQL query to be prepared and executed.
     *
     * @var string
     */
    protected $_query;

    /**
     * The previously executed SQL query.
     *
     * @var string
     */
    protected $_lastQuery;

    /**
     * The SQL query options required after SELECT, INSERT, UPDATE or DELETE.
     *
     * @var array
     */
    protected $_queryOptions = [];

    /**
     * An array that holds where joins.
     *
     * @var array
     */
    protected $_join = [];

    /**
     * An array that holds where conditions.
     *
     * @var array
     */
    protected $_where = [];

    /**
     * An array that holds where join ands.
     *
     * @var array
     */
    protected $_joinAnd = [];

    /**
     * An array that holds having conditions.
     *
     * @var array
     */
    protected $_having = [];

    /**
     * Dynamic type list for order by condition value.
     *
     * @var array
     */
    protected $_orderBy = [];

    /**
     * Dynamic type list for group by condition value.
     *
     * @var array
     */
    protected $_groupBy = [];


    /**
     * Dynamic array that holds a combination of where condition/table data value types and parameter references.
     *
     * @var array
     */
    protected $_bindParams = ['']; // Create the empty 0 index

    /**
     * Variable which holds last statement error.
     *
     * @var string
     */
    protected $_stmtError;

    /**
     * Variable which holds last statement error code.
     *
     * @var int
     */
    protected $_stmtErrno;

    /**
     * Is Subquery object.
     *
     * @var bool
     */
    protected $isSubQuery = false;

    /**
     * Name of the auto increment column.
     *
     * @var int
     */
    protected $_lastInsertId;

    /**
     * Column names for update when using onDuplicate method.
     *
     * @var array
     */
    protected $_updateColumns;

    /**
     * Should join() results be nested by table.
     *
     * @var bool
     */
    protected $_nestJoin = false;

    /**
     * FOR UPDATE flag.
     *
     * @var bool
     */
    protected $_forUpdate = false;


    /**
     * Key field for Map()'ed result array.
     *
     * @var string
     */
    protected $_mapKey;

    /**
     * Variables for query execution tracing.
     */
    protected $traceStartQ;
    protected $traceEnabled;
    protected $traceStripPrefix;

    /**
     * @var array connections settings [profile_name=>[same_as_contruct_args]]
     */
    protected $connectionsSettings = [];
    protected $autoReconnectCount = 0;

    /**
     * @var bool Operations in transaction indicator
     */

    /**
     * Table name (with prefix, if used).
     *
     * @var string
     */
    private $_tableName = '';

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $db
     * @param int    $port
     * @param string $charset
     * @param string $socket
     */
    public function __construct($host = null, $username = null, $password = null, $db = null, $port = null, $charset = 'utf8', $socket = null)
    {
        $isSubQuery = false;

        // if params were passed as array
        if (\is_array($host)) {
            foreach ($host as $key => $val) {
                ${$key} = $val;
            }
        }

        $this->addConnection('default', [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'db' => $db,
            'port' => $port,
            'socket' => $socket,
            'charset' => $charset,
        ]);

        if ($isSubQuery) {
            $this->isSubQuery = true;

            return;
        }

        if (isset($prefix)) {
            $this->setPrefix($prefix);
        }

        $this->db_name = $db;

        self::$_instance = $this;
    }

    /**
     * A method to connect to the database.
     *
     * @param string|null $connectionName
     *
     * @throws \Exception
     */
    public function connect($connectionName = 'default')
    {
        if (!isset($this->connectionsSettings[$connectionName])) {
            throw new \Exception('Connection profile not set');
        }

        $pro = $this->connectionsSettings[$connectionName];
        $params = array_values($pro);
        $charset = array_pop($params);

        if ($this->isSubQuery) {
            return;
        }

        if (empty($pro['host']) && empty($pro['socket'])) {
            throw new \Exception('MySQL host or socket is not set');
        }

        $mysqlic = new \ReflectionClass('mysqli');
        $mysqli = $mysqlic->newInstanceArgs($params);

        if ($mysqli->connect_error) {
            throw new \Exception('Connect Error '.$mysqli->connect_errno.': '.$mysqli->connect_error, $mysqli->connect_errno);
        }

        if (!empty($charset)) {
            $mysqli->set_charset($charset);
        }
        $this->_mysqli[$connectionName] = $mysqli;
    }

    /**
     * @throws \Exception
     */
    public function disconnectAll()
    {
        foreach (array_keys($this->_mysqli) as $k) {
            $this->disconnect($k);
        }
    }

    /**
     * Set the connection name to use in the next query.
     *
     * @param string $name
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function connection($name)
    {
        if (!isset($this->connectionsSettings[$name])) {
            throw new \Exception('Connection '.$name.' was not added.');
        }

        $this->defConnectionName = $name;

        return $this;
    }

    /**
     * A method to disconnect from the database.
     *
     * @params string $connection connection name to disconnect
     *
     * @param string $connection
     */
    public function disconnect($connection = 'default')
    {
        if (!isset($this->_mysqli[$connection])) {
            return;
        }

        $this->_mysqli[$connection]->close();
        unset($this->_mysqli[$connection]);
    }

    /**
     * Create & store at _mysqli new mysqli instance.
     *
     * @param string $name
     *
     * @return $this
     */
    public function addConnection($name, array $params)
    {
        $this->connectionsSettings[$name] = [];
        foreach (['host', 'username', 'password', 'db', 'port', 'socket', 'charset'] as $k) {
            $prm = isset($params[$k]) ? $params[$k] : null;

            if ('host' == $k) {
                if (\is_object($prm)) {
                    $this->_mysqli[$name] = $prm;
                }

                if (!\is_string($prm)) {
                    $prm = null;
                }
            }
            $this->connectionsSettings[$name][$k] = $prm;
        }

        return $this;
    }

    /**
     * A method to get mysqli object or create it in case needed.
     *
     * @return mysqli
     *
     * @throws \Exception
     */
    public function mysqli()
    {
        if (!isset($this->_mysqli[$this->defConnectionName])) {
            $this->connect($this->defConnectionName);
        }

        return $this->_mysqli[$this->defConnectionName];
    }

    /**
     * Helper function to create dbObject with JSON return type.
     *
     * @return MysqliDb
     */
    public function jsonBuilder()
    {
        $this->returnType = 'json';

        return $this;
    }

    /**
     * Helper function to create dbObject with array return type
     * Added for consistency as that's default output type.
     *
     * @return MysqliDb
     */
    public function arrayBuilder()
    {
        $this->returnType = 'array';

        return $this;
    }

    /**
     * Helper function to create dbObject with object return type.
     *
     * @return MysqliDb
     */
    public function objectBuilder()
    {
        $this->returnType = 'object';

        return $this;
    }

    /**
     * Prefix add raw SQL query.
     *
     * @author Emre Emir <https://github.com/bejutassle>
     *
     * @param string $query user-provided query to execute
     *
     * @return string contains the returned rows from the query
     */
    public function rawAddPrefix($query)
    {
        $query = str_replace(\PHP_EOL, '', $query);
        $query = preg_replace('/\s+/', ' ', $query);

        return preg_replace("/(from|into|update|join|describe) [\\'\\´]?([a-zA-Z0-9_-]+)[\\'\\´]?/iA", '$1 '.self::$prefix.'$2', $query);
    }

    /**
     * Execute raw SQL query.
     *
     * @param string $query      user-provided query to execute
     * @param array  $bindParams variables array to bind to the SQL statement
     *
     * @return array contains the returned rows from the query
     *
     * @throws \Exception
     */
    public function rawQuery($query, $bindParams = null)
    {
        $query = $this->rawAddPrefix($query);
        $params = ['']; // Create the empty 0 index
        $this->_query = $query;

        $stmt = $this->_prepareQuery();

        if (true === \is_array($bindParams)) {
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->_determineType($val);
                $params[] = $bindParams[$prop];
            }

            \call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
        }

        $stmt->execute();
        $this->count = $stmt->affected_rows;
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $params);
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /**
     * Helper function to execute raw SQL query and return only 1 row of results.
     * Note that function do not add 'limit 1' to the query by itself
     * Same idea as getOne().
     *
     * @param string $query      user-provided query to execute
     * @param array  $bindParams variables array to bind to the SQL statement
     *
     * @return array|null contains the returned row from the query
     *
     * @throws \Exception
     */
    public function rawQueryOne($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (\is_array($res) && isset($res[0])) {
            return $res[0];
        }

        return null;
    }

    /**
     * Helper function to execute raw SQL query and return only 1 column of results.
     * If 'limit 1' will be found, then string will be returned instead of array
     * Same idea as getValue().
     *
     * @param string $query      user-provided query to execute
     * @param array  $bindParams variables array to bind to the SQL statement
     *
     * @return mixed contains the returned rows from the query
     *
     * @throws \Exception
     */
    public function rawQueryValue($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (!$res) {
            return null;
        }

        $limit = preg_match('/limit\s+1;?$/i', $query);
        $key = key($res[0]);
        if (isset($res[0][$key]) && true == $limit) {
            return $res[0][$key];
        }

        $newRes = [];
        for ($i = 0; $i < $this->count; ++$i) {
            $newRes[] = $res[$i][$key];
        }

        return $newRes;
    }

    /**
     * A method to perform select query.
     *
     * @param string    $query   contains a user-provided select query
     * @param array|int $numRows Array to define SQL limit in format Array ($offset, $count)
     *
     * @return array contains the returned rows from the query
     *
     * @throws \Exception
     */
    public function query($query, $numRows = null)
    {
        $this->_query = $query;
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /**
     * Function to enable SQL_CALC_FOUND_ROWS in the get queries.
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function withTotalCount()
    {
        $this->setQueryOption('SQL_CALC_FOUND_ROWS');

        return $this;
    }

    /**
     * Insert method to add new row.
     *
     * @param string $tableName  the name of the table
     * @param array  $insertData data containing information for inserting into the DB
     *
     * @return bool boolean indicating whether the insert query was completed successfully
     *
     * @throws \Exception
     */
    public function insert($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'INSERT');
    }

    /**
     * Insert method to add several rows at once.
     *
     * @param string $tableName       the name of the table
     * @param array  $multiInsertData two-dimensional Data-array containing information for inserting into the DB
     * @param array  $dataKeys        optional Table Key names, if not set in insertDataSet
     *
     * @return array|bool Boolean indicating the insertion failed (false), else return id-array ([int])
     *
     * @throws \Exception
     */
    public function insertMulti($tableName, array $multiInsertData, ?array $dataKeys = null)
    {
        // only auto-commit our inserts, if no transaction is currently running
        $autoCommit = (isset($this->_transaction_in_progress) ? !$this->_transaction_in_progress : true);
        $ids = [];

        if ($autoCommit) {
            $this->startTransaction();
        }

        foreach ($multiInsertData as $insertData) {
            if (null !== $dataKeys) {
                // apply column-names if given, else assume they're already given in the data
                $insertData = array_combine($dataKeys, $insertData);
            }

            $id = $this->insert($tableName, $insertData);
            if (!$id) {
                if ($autoCommit) {
                    $this->rollback();
                }

                return false;
            }
            $ids[] = $id;
        }

        if ($autoCommit) {
            $this->commit();
        }

        return $ids;
    }

    /**
     * Replace method to add new row.
     *
     * @param string $tableName  the name of the table
     * @param array  $insertData data containing information for inserting into the DB
     *
     * @return bool boolean indicating whether the insert query was completed successfully
     *
     * @throws \Exception
     */
    public function replace($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'REPLACE');
    }

    /**
     * A convenient function that returns TRUE if exists at least an element that
     * satisfy the where condition specified calling the "where" method before this one.
     *
     * @param string $tableName the name of the database table to work with
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function has($tableName)
    {
        $this->getOne($tableName, '1');

        return $this->count >= 1;
    }

    /**
     * Update query. Be sure to first call the "where" method.
     *
     * @param string $tableName the name of the database table to work with
     * @param array  $tableData array of data to update the desired row
     * @param int    $numRows   limit on the number of rows that can be updated
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function update($tableName, $tableData, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }

        $this->_query = 'UPDATE '.self::$prefix.$tableName;

        $stmt = $this->_buildQuery($numRows, $tableData);
        $status = $stmt->execute();
        $this->reset();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->count = $stmt->affected_rows;

        return $status;
    }

    /**
     * Delete query. Call the "where" method first.
     *
     * @param string    $tableName the name of the database table to work with
     * @param array|int $numRows   Array to define SQL limit in format Array ($offset, $count)
     *                             or only $count
     *
     * @return bool Indicates success. 0 or 1.
     *
     * @throws \Exception
     */
    public function delete($tableName, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }

        $table = self::$prefix.$tableName;

        if (\count($this->_join)) {
            $this->_query = 'DELETE '.preg_replace('/.* (.*)/', '$1', $table).' FROM '.$table;
        } else {
            $this->_query = 'DELETE FROM '.$table;
        }

        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->count = $stmt->affected_rows;
        $this->reset();

        return $stmt->affected_rows > -1;    //	-1 indicates that the query returned an error
    }

    /**
     * This method allows you to specify multiple (method chaining optional) AND WHERE statements for SQL queries.
     *
     * @uses $MySqliDb->where('id', 7)->where('title', 'MyTitle');
     *
     * @param string $whereProp  the name of the database field
     * @param mixed  $whereValue the value of the database field
     * @param string $operator   Comparison operator. Default is =
     * @param string $cond       Condition of where statement (OR, AND)
     *
     * @return MysqliDb
     */
    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        if (0 == \count($this->_where)) {
            $cond = '';
        }

        $this->_where[] = [$cond, $whereProp, $operator, $whereValue];

        return $this;
    }

    /**
     * This function store update column's name and column name of the
     * autoincrement column.
     *
     * @param array  $updateColumns Variable with values
     * @param string $lastInsertId  Variable value
     *
     * @return MysqliDb
     */
    public function onDuplicate($updateColumns, $lastInsertId = null)
    {
        $this->_lastInsertId = $lastInsertId;
        $this->_updateColumns = $updateColumns;

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) OR WHERE statements for SQL queries.
     *
     * @uses $MySqliDb->orWhere('id', 7)->orWhere('title', 'MyTitle');
     *
     * @param string $whereProp  the name of the database field
     * @param mixed  $whereValue the value of the database field
     * @param string $operator   Comparison operator. Default is =
     *
     * @return MysqliDb
     */
    public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->where($whereProp, $whereValue, $operator, 'OR');
    }

    /**
     * This method allows you to specify multiple (method chaining optional) AND HAVING statements for SQL queries.
     *
     * @uses $MySqliDb->having('SUM(tags) > 10')
     *
     * @param string $havingProp  the name of the database field
     * @param mixed  $havingValue the value of the database field
     * @param string $operator    Comparison operator. Default is =
     * @param string $cond
     *
     * @return MysqliDb
     */
    public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        // forkaround for an old operation api
        if (\is_array($havingValue) && ($key = key($havingValue)) != '0') {
            $operator = $key;
            $havingValue = $havingValue[$key];
        }

        if (0 == \count($this->_having)) {
            $cond = '';
        }

        $this->_having[] = [$cond, $havingProp, $operator, $havingValue];

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) OR HAVING statements for SQL queries.
     *
     * @uses $MySqliDb->orHaving('SUM(tags) > 10')
     *
     * @param string $havingProp  the name of the database field
     * @param mixed  $havingValue the value of the database field
     * @param string $operator    Comparison operator. Default is =
     *
     * @return MysqliDb
     */
    public function orHaving($havingProp, $havingValue = null, $operator = null)
    {
        return $this->having($havingProp, $havingValue, $operator, 'OR');
    }

    /**
     * This method allows you to concatenate joins for the final SQL statement.
     *
     * @uses $MySqliDb->join('table1', 'field1 <> field2', 'LEFT')
     *
     * @param string $joinTable     the name of the table
     * @param string $joinCondition the condition
     * @param string $joinType      'LEFT', 'INNER' etc
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function join($joinTable, $joinCondition, $joinType = '')
    {
        $allowedTypes = ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER', 'NATURAL'];
        $joinType = strtoupper(trim($joinType));

        if ($joinType && !\in_array($joinType, $allowedTypes)) {
            throw new \Exception('Wrong JOIN type: '.$joinType);
        }

        if (!\is_object($joinTable)) {
            $joinTable = self::$prefix.$joinTable;
        }

        $this->_join[] = [$joinType, $joinTable, $joinCondition];

        return $this;
    }

    /**
     * This is a basic method which allows you to import raw .CSV data into a table
     * Please check out http://dev.mysql.com/doc/refman/5.7/en/load-data.html for a valid .csv file.
     *
     * @author Jonas Barascu (Noneatme)
     *
     * @param string $importTable    the database table where the data will be imported into
     * @param string $importFile     The file to be imported. Please use double backslashes \\ and make sure you
     * @param string $importSettings An Array defining the import settings as described in the README.md
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function loadData($importTable, $importFile, $importSettings = null)
    {
        // We have to check if the file exists
        if (!file_exists($importFile)) {
            // Throw an exception
            throw new \Exception('importCSV -> importFile '.$importFile.' does not exists!');
        }

        // Define the default values
        // We will merge it later
        $settings = ['fieldChar' => ';', 'lineChar' => \PHP_EOL, 'linesToIgnore' => 1];

        // Check the import settings
        if ('array' == \gettype($importSettings)) {
            // Merge the default array with the custom one
            $settings = array_merge($settings, $importSettings);
        }

        // Add the prefix to the import table
        $table = self::$prefix.$importTable;

        // Add 1 more slash to every slash so maria will interpret it as a path
        $importFile = str_replace('\\', '\\\\', $importFile);

        // Switch between LOAD DATA and LOAD DATA LOCAL
        $loadDataLocal = isset($settings['loadDataLocal']) ? 'LOCAL' : '';

        // Build SQL Syntax
        $sqlSyntax = sprintf('LOAD DATA %s INFILE \'%s\' INTO TABLE %s', $loadDataLocal, $importFile, $table);

        // FIELDS
        $sqlSyntax .= sprintf(' FIELDS TERMINATED BY \'%s\'', $settings['fieldChar']);
        if (isset($settings['fieldEnclosure'])) {
            $sqlSyntax .= sprintf(' ENCLOSED BY \'%s\'', $settings['fieldEnclosure']);
        }

        // LINES
        $sqlSyntax .= sprintf(' LINES TERMINATED BY \'%s\'', $settings['lineChar']);
        if (isset($settings['lineStarting'])) {
            $sqlSyntax .= sprintf(' STARTING BY \'%s\'', $settings['lineStarting']);
        }

        // IGNORE LINES
        $sqlSyntax .= sprintf(' IGNORE %d LINES', $settings['linesToIgnore']);

        // Execute the query unprepared because LOAD DATA only works with unprepared statements.
        $result = $this->queryUnprepared($sqlSyntax);

        // Are there rows modified?
        // Let the user know if the import failed / succeeded
        return (bool) $result;
    }

    /**
     * This method is useful for importing XML files into a specific table.
     * Check out the LOAD XML syntax for your MySQL server.
     *
     * @author Jonas Barascu
     *
     * @param string $importTable    the table in which the data will be imported to
     * @param string $importFile     The file which contains the .XML data.
     * @param string $importSettings An Array defining the import settings as described in the README.md
     *
     * @return bool returns true if the import succeeded, false if it failed
     *
     * @throws \Exception
     */
    public function loadXml($importTable, $importFile, $importSettings = null)
    {
        // We have to check if the file exists
        if (!file_exists($importFile)) {
            // Does not exists
            throw new \Exception('loadXml: Import file does not exists');

            return;
        }

        // Create default values
        $settings = ['linesToIgnore' => 0];

        // Check the import settings
        if ('array' == \gettype($importSettings)) {
            $settings = array_merge($settings, $importSettings);
        }

        // Add the prefix to the import table
        $table = self::$prefix.$importTable;

        // Add 1 more slash to every slash so maria will interpret it as a path
        $importFile = str_replace('\\', '\\\\', $importFile);

        // Build SQL Syntax
        $sqlSyntax = sprintf('LOAD XML INFILE \'%s\' INTO TABLE %s', $importFile, $table);

        // FIELDS
        if (isset($settings['rowTag'])) {
            $sqlSyntax .= sprintf(' ROWS IDENTIFIED BY \'%s\'', $settings['rowTag']);
        }

        // IGNORE LINES
        $sqlSyntax .= sprintf(' IGNORE %d LINES', $settings['linesToIgnore']);

        // Exceute the query unprepared because LOAD XML only works with unprepared statements.
        $result = $this->queryUnprepared($sqlSyntax);

        // Are there rows modified?
        // Let the user know if the import failed / succeeded
        return (bool) $result;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) ORDER BY statements for SQL queries.
     *
     * @uses $MySqliDb->orderBy('id', 'desc')->orderBy('name', 'desc', '^[a-z]')->orderBy('name', 'desc');
     *
     * @param string $orderByField         the name of the database field
     * @param string $orderbyDirection
     * @param mixed  $customFieldsOrRegExp Array with fieldset for ORDER BY FIELD() ordering or string with regular expression for ORDER BY REGEXP ordering
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function orderBy($orderByField, $orderbyDirection = 'DESC', $customFieldsOrRegExp = null)
    {
        $allowedDirection = ['ASC', 'DESC'];
        $orderbyDirection = strtoupper(trim($orderbyDirection));
        $orderByField = preg_replace("/[^ -a-z0-9\\.\\(\\),_`\\*\\'\"]+/i", '', $orderByField);

        // Add table prefix to orderByField if needed.
        // FIXME: We are adding prefix only if table is enclosed into `` to distinguish aliases
        // from table names
        $orderByField = preg_replace('/(\`)([`a-zA-Z0-9_]*\.)/', '\1'.self::$prefix.'\2', $orderByField);

        if (empty($orderbyDirection) || !\in_array($orderbyDirection, $allowedDirection)) {
            throw new \Exception('Wrong order direction: '.$orderbyDirection);
        }

        if (\is_array($customFieldsOrRegExp)) {
            foreach ($customFieldsOrRegExp as $key => $value) {
                $customFieldsOrRegExp[$key] = preg_replace("/[^\x80-\xff-a-z0-9\\.\\(\\),_` ]+/i", '', $value);
            }
            $orderByField = 'FIELD ('.$orderByField.', "'.implode('","', $customFieldsOrRegExp).'")';
        } elseif (\is_string($customFieldsOrRegExp)) {
            $orderByField = $orderByField." REGEXP '".$customFieldsOrRegExp."'";
        } elseif (null !== $customFieldsOrRegExp) {
            throw new \Exception('Wrong custom field or Regular Expression: '.$customFieldsOrRegExp);
        }

        $this->_orderBy[$orderByField] = $orderbyDirection;

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) GROUP BY statements for SQL queries.
     *
     * @uses $MySqliDb->groupBy('name');
     *
     * @param string $groupByField the name of the database field
     *
     * @return MysqliDb
     */
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace('/[^-a-z0-9\\.\\(\\),_\\* <>=!]+/i', '', $groupByField);

        $this->_groupBy[] = $groupByField;

        return $this;
    }

    /**
     * Escape harmful characters which might affect a query.
     *
     * @param string $str the string to escape
     *
     * @return string the escaped string
     *
     * @throws \Exception
     */
    public function escape($str)
    {
        return $this->mysqli()->real_escape_string($str);
    }

    /**
     * Method to call mysqli->ping() to keep unused connections open on
     * long-running scripts, or to reconnect timed out connections (if php.ini has
     * global mysqli.reconnect set to true). Can't do this directly using object
     * since _mysqli is protected.
     *
     * @return bool True if connection is up
     *
     * @throws \Exception
     */
    public function ping()
    {
        return $this->mysqli()->ping();
    }

    /**
     * Insert/Update query helper.
     *
     * @param array $tableData
     * @param array $tableColumns
     * @param bool  $isInsert     INSERT operation flag
     *
     * @throws \Exception
     */
    public function _buildDataPairs($tableData, $tableColumns, $isInsert)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];

            if (!$isInsert) {
                if (!str_contains($column, '.')) {
                    $this->_query .= '`'.$column.'` = ';
                } else {
                    $this->_query .= str_replace('.', '.`', $column).'` = ';
                }
            }

            // Subquery value
            if ($value instanceof self) {
                $this->_query .= $this->_buildPair('', $value).', ';

                continue;
            }

            // Simple value
            if (!\is_array($value)) {
                $this->_bindParam($value);
                $this->_query .= '?, ';

                continue;
            }

            // Function value
            $key = key($value);
            $val = $value[$key];

            switch ($key) {
                case '[I]':
                    $this->_query .= $column.$val.', ';

                    break;

                case '[F]':
                    $this->_query .= $val[0].', ';
                    if (!empty($val[1])) {
                        $this->_bindParams($val[1]);
                    }

                    break;

                case '[N]':
                    if (null == $val) {
                        $this->_query .= '!'.$column.', ';
                    } else {
                        $this->_query .= '!'.$val.', ';
                    }

                    break;

                default:
                    throw new \Exception('Wrong operation');
            }
        }
        $this->_query = rtrim($this->_query, ', ');
    }

    // Helper functions

    /**
     * Method returns generated interval function as a string.
     *
     * @param string $diff interval in the formats:
     *                     "1", "-1d" or "- 1 day" -- For interval - 1 day
     *                     Supported intervals [s]econd, [m]inute, [h]hour, [d]day, [M]onth, [Y]ear
     *                     Default null;
     * @param string $func Initial date
     *
     * @return string
     *
     * @throws \Exception
     */
    public function interval($diff, $func = 'NOW()')
    {
        $types = ['s' => 'second', 'm' => 'minute', 'h' => 'hour', 'd' => 'day', 'M' => 'month', 'Y' => 'year'];
        $incr = '+';
        $items = '';
        $type = 'd';

        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }

            if (!empty($matches[2])) {
                $items = $matches[2];
            }

            if (!empty($matches[3])) {
                $type = $matches[3];
            }

            if (!\in_array($type, array_keys($types))) {
                throw new \Exception("invalid interval type in '{$diff}'");
            }

            $func .= ' '.$incr.' interval '.$items.' '.$types[$type].' ';
        }

        return $func;
    }

    /**
     * Method returns generated interval function as an insert/update function.
     *
     * @param string $diff interval in the formats:
     *                     "1", "-1d" or "- 1 day" -- For interval - 1 day
     *                     Supported intervals [s]econd, [m]inute, [h]hour, [d]day, [M]onth, [Y]ear
     *                     Default null;
     * @param string $func Initial date
     *
     * @return array
     *
     * @throws \Exception
     */
    public function now($diff = null, $func = 'NOW()')
    {
        return ['[F]' => [$this->interval($diff, $func)]];
    }

    /**
     * Method generates incremental function call.
     *
     * @param int $num increment by int or float. 1 by default
     *
     * @return array
     *
     * @throws \Exception
     */
    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new \Exception('Argument supplied to inc must be a number');
        }

        return ['[I]' => '+'.$num];
    }

    /**
     * Method generates decremental function call.
     *
     * @param int $num increment by int or float. 1 by default
     *
     * @return array
     *
     * @throws \Exception
     */
    public function dec($num = 1)
    {
        if (!is_numeric($num)) {
            throw new \Exception('Argument supplied to dec must be a number');
        }

        return ['[I]' => '-'.$num];
    }

    /**
     * Method generates change boolean function call.
     *
     * @param string $col column name. null by default
     *
     * @return array
     */
    public function not($col = null)
    {
        return ['[N]' => (string) $col];
    }

    /**
     * Method generates user defined function call.
     *
     * @param string $expr       user function body
     * @param array  $bindParams
     *
     * @return array
     */
    public function func($expr, $bindParams = null)
    {
        return ['[F]' => [$expr, $bindParams]];
    }

    /**
     * Method creates new mysqlidb object for a subquery generation.
     *
     * @param string $subQueryAlias
     *
     * @return MysqliDb
     */
    public static function subQuery($subQueryAlias = '')
    {
        return new self(['host' => $subQueryAlias, 'isSubQuery' => true]);
    }

    /**
     * Method returns a copy of a mysqlidb subquery object.
     *
     * @return MysqliDb new mysqlidb object
     */
    public function copy()
    {
        $copy = unserialize(serialize($this));
        $copy->_mysqli = [];

        return $copy;
    }


    /**
     * Method to check if needed table is created.
     *
     * @param array $tables Table name or an Array of table names to check
     *
     * @return bool True if table exists
     *
     * @throws \Exception
     */
    public function tableExists($tables)
    {
        $tables = !\is_array($tables) ? [$tables] : $tables;
        $count = \count($tables);
        if (0 == $count) {
            return false;
        }

        foreach ($tables as $i => $value) {
            $tables[$i] = self::$prefix.$value;
        }
        $db = isset($this->connectionsSettings[$this->defConnectionName]) ? $this->connectionsSettings[$this->defConnectionName]['db'] : null;
        $this->where('table_schema', $db);
        $this->where('table_name', $tables, 'in');
        $this->get('information_schema.tables', $count);

        return $this->count == $count;
    }

    /**
     * Return result as an associative array with $idField field value used as a record key.
     *
     * Array Returns an array($k => $v) if get(.."param1, param2"), array ($k => array ($v, $v)) otherwise
     *
     * @param string $idField field name to use for a mapped element key
     *
     * @return MysqliDb
     */
    public function map($idField)
    {
        $this->_mapKey = $idField;

        return $this;
    }

    /**
     * Pagination wrapper to get().
     *
     * @param string       $table  The name of the database table to work with
     * @param int          $page   Page number
     * @param array|string $fields Array or coma separated list of fields to fetch
     *
     * @return array
     *
     * @throws \Exception
     */
    public function paginate($table, $page, $fields = null)
    {
        $offset = $this->pageLimit * ($page - 1);
        $res = $this->withTotalCount()->get($table, [$offset, $this->pageLimit], $fields);
        $this->totalPages = ceil($this->totalCount / $this->pageLimit);

        return $res;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) AND WHERE statements for the join table on part of the SQL query.
     *
     * @uses $dbWrapper->joinWhere('user u', 'u.id', 7)->where('user u', 'u.title', 'MyTitle');
     *
     * @param string $whereJoin  the name of the table followed by its prefix
     * @param string $whereProp  the name of the database field
     * @param mixed  $whereValue the value of the database field
     * @param string $operator
     * @param string $cond
     *
     * @return $this
     */
    public function joinWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->_joinAnd[self::$prefix.$whereJoin][] = [$cond, $whereProp, $operator, $whereValue];

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) OR WHERE statements for the join table on part of the SQL query.
     *
     * @uses $dbWrapper->joinWhere('user u', 'u.id', 7)->where('user u', 'u.title', 'MyTitle');
     *
     * @param string $whereJoin  the name of the table followed by its prefix
     * @param string $whereProp  the name of the database field
     * @param mixed  $whereValue the value of the database field
     * @param string $operator
     *
     * @return $this
     */
    public function joinOrWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        return $this->joinWhere($whereJoin, $whereProp, $whereValue, $operator, 'OR');
    }
}
