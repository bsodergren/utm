<?php

namespace UTM\Bundle\mysql\traits;

trait MySQLDbGet
{
    /**
     * A method to get the name of the db.
     *
     * @return db_name
     */
    public function getdbName()
    {
        return $this->db_name;
    }

    /**
     * A method of returning the static instance to allow access to the
     * instantiated object from within another class.
     * Inheriting this class would require reloading connection info.
     *
     * @uses $db = MySqliDb::getInstance();
     *
     * @return MysqliDb returns the current instance
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * A convenient SELECT * function.
     *
     * @param string       $tableName the name of the database table to work with
     * @param array|int    $numRows   Array to define SQL limit in format Array ($offset, $count)
     *                                or only $count
     * @param array|string $columns   Desired columns
     *
     * @return array|MysqliDb contains the returned rows from the select query
     *
     * @throws \Exception
     */
    public function get($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }

        $column = \is_array($columns) ? implode(', ', $columns) : $columns;

        if (!str_contains($tableName, '.')) {
            $this->_tableName = self::$prefix.$tableName;
        } else {
            $this->_tableName = $tableName;
        }

        $this->_query = 'SELECT '.implode(' ', $this->_queryOptions).' '.
            $column.' FROM '.$this->_tableName;
        $stmt = $this->_buildQuery($numRows);

        if ($this->isSubQuery) {
            return $this;
        }

        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /**
     * A convenient SELECT * function to get one record.
     *
     * @param string       $tableName the name of the database table to work with
     * @param array|string $columns   Desired columns
     *
     * @return array contains the returned rows from the select query
     *
     * @throws \Exception
     */
    public function getOne($tableName, $columns = '*')
    {
        $res = $this->get($tableName, 1, $columns);

        if ($res instanceof self) {
            return $res;
        }
        if (\is_array($res) && isset($res[0])) {
            return $res[0];
        }
        if ($res) {
            return $res;
        }

        return null;
    }

    /**
     * A convenient SELECT COLUMN function to get a single column value from one row.
     *
     * @param string $tableName the name of the database table to work with
     * @param string $column    The desired column
     * @param int    $limit     Limit of rows to select. Use null for unlimited..1 by default
     *
     * @return mixed Contains the value of a returned column / array of values
     *
     * @throws \Exception
     */
    public function getValue($tableName, $column, $limit = 1)
    {
        $res = $this->ArrayBuilder()->get($tableName, $limit, "{$column} AS retval");

        if (!$res) {
            return null;
        }

        if (1 == $limit) {
            if (isset($res[0]['retval'])) {
                return $res[0]['retval'];
            }

            return null;
        }

        $newRes = [];
        for ($i = 0; $i < $this->count; ++$i) {
            $newRes[] = $res[$i]['retval'];
        }

        return $newRes;
    }

    /**
     * This methods returns the ID of the last inserted item.
     *
     * @return int the last inserted item ID
     *
     * @throws \Exception
     */
    public function getInsertId()
    {
        return $this->mysqli()->insert_id;
    }

    /**
     * Method returns last executed query.
     *
     * @return array
     */
    public function getLastQuery()
    {
        return [$this->_lastQuery];
    }

    /**
     * Method returns mysql error.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getLastError()
    {
        if (!isset($this->_mysqli[$this->defConnectionName])) {
            return 'mysqli is null';
        }

        return trim($this->_stmtError.' '.$this->mysqli()->error);
    }

    /**
     * Method returns mysql error code.
     *
     * @return int
     */
    public function getLastErrno()
    {
        return $this->_stmtErrno;
    }

    /**
     * Mostly internal method to get query and its params out of subquery object
     * after get() and getAll().
     *
     * @return array
     */
    public function getSubQuery()
    {
        if (!$this->isSubQuery) {
            return null;
        }

        array_shift($this->_bindParams);
        $val = [
            'query' => $this->_query,
            'params' => $this->_bindParams,
            'alias' => isset($this->connectionsSettings[$this->defConnectionName]) ? $this->connectionsSettings[$this->defConnectionName]['host'] : null,
        ];
        $this->reset();

        return $val;
    }
}
