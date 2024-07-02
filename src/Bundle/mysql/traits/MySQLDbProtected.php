<?php

namespace UTM\Bundle\mysql\traits;

trait MySQLDbProtected
{
    /**
     * Reset states after an execution.
     *
     * @return MysqliDb returns the current instance
     */
    protected function reset()
    {
        if ($this->traceEnabled) {
            $this->trace[] = [$this->_lastQuery, microtime(true) - $this->traceStartQ, $this->_traceGetCaller()];
        }

        $this->_where = [];
        $this->_having = [];
        $this->_join = [];
        $this->_joinAnd = [];
        $this->_orderBy = [];
        $this->_groupBy = [];
        $this->_bindParams = ['']; // Create the empty 0 index
        $this->_query = null;
        $this->_queryOptions = [];
        $this->returnType = 'array';
        $this->_nestJoin = false;
        $this->_forUpdate = false;
        $this->_lockInShareMode = false;
        $this->_tableName = '';
        $this->_lastInsertId = null;
        $this->_updateColumns = null;
        $this->_mapKey = null;
        if (!$this->_transaction_in_progress) {
            $this->defConnectionName = 'default';
        }
        $this->autoReconnectCount = 0;

        return $this;
    }

    /**
     * This method is needed for prepared statements. They require
     * the data type of the field to be bound with "i" s", etc.
     * This function takes the input, determines what type it is,
     * and then updates the param_type.
     *
     * @param mixed $item input to determine the type
     *
     * @return string the joined parameter types
     */
    protected function _determineType($item)
    {
        switch (\gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';

                break;

            case 'boolean':
            case 'integer':
                return 'i';

                break;

            case 'blob':
                return 'b';

                break;

            case 'double':
                return 'd';

                break;
        }

        return '';
    }

    /**
     * Helper function to add variables into bind parameters array.
     *
     * @param string Variable value
     */
    protected function _bindParam($value)
    {
        $this->_bindParams[0] .= $this->_determineType($value);
        $this->_bindParams[] = $value;
    }

    /**
     * Helper function to add variables into bind parameters array in bulk.
     *
     * @param array $values Variable with values
     */
    protected function _bindParams($values)
    {
        foreach ($values as $value) {
            $this->_bindParam($value);
        }
    }

    /**
     * Helper function to add variables into bind parameters array and will return
     * its SQL part of the query according to operator in ' $operator ?' or
     * ' $operator ($subquery) ' formats.
     *
     * @param string $operator
     * @param mixed  $value    Variable with values
     *
     * @return string
     */
    protected function _buildPair($operator, $value)
    {
        if (!\is_object($value)) {
            $this->_bindParam($value);

            return ' '.$operator.' ? ';
        }

        $subQuery = $value->getSubQuery();
        $this->_bindParams($subQuery['params']);

        return ' '.$operator.' ('.$subQuery['query'].') '.$subQuery['alias'];
    }

    /**
     * Abstraction method that will compile the WHERE statement,
     * any passed update data, and the desired rows.
     * It then builds the SQL query.
     *
     * @param array|int $numRows   Array to define SQL limit in format Array ($offset, $count)
     *                             or only $count
     * @param array     $tableData should contain an array of data for updating the database
     *
     * @return bool|\mysqli_stmt returns the $stmt object
     *
     * @throws \Exception
     */
    protected function _buildQuery($numRows = null, $tableData = null)
    {
        // $this->_buildJoinOld();
        $this->_buildJoin();
        $this->_buildInsertQuery($tableData);
        $this->_buildCondition('WHERE', $this->_where);
        $this->_buildGroupBy();
        $this->_buildCondition('HAVING', $this->_having);
        $this->_buildOrderBy();
        $this->_buildLimit($numRows);
        $this->_buildOnDuplicate($tableData);

        if ($this->_forUpdate) {
            $this->_query .= ' FOR UPDATE';
        }
        if ($this->_lockInShareMode) {
            $this->_query .= ' LOCK IN SHARE MODE';
        }

        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $this->_bindParams);

        if ($this->isSubQuery) {
            return;
        }

        // Prepare query
        $stmt = $this->_prepareQuery();

        // Bind parameters to statement if any
        if (\count($this->_bindParams) > 1) {
            \call_user_func_array([$stmt, 'bind_param'], $this->refValues($this->_bindParams));
        }

        return $stmt;
    }

    /**
     * This helper method takes care of prepared statements' "bind_result method
     * , when the number of variables to pass is unknown.
     *
     * @param \mysqli_stmt $stmt equal to the prepared statement object
     *
     * @return array|string the results of the SQL fetch
     *
     * @throws \Exception
     */
    protected function _dynamicBindResults(\mysqli_stmt $stmt)
    {
        $parameters = [];
        $results = [];

        /**
         * @see http://php.net/manual/en/mysqli-result.fetch-fields.php
         */
        $mysqlLongType = 252;
        $shouldStoreResult = false;

        $meta = $stmt->result_metadata();

        // if $meta is false yet sqlstate is true, there's no sql error but the query is
        // most likely an update/insert/delete which doesn't produce any results
        if (!$meta && $stmt->sqlstate) {
            return [];
        }

        $row = [];
        while ($field = $meta->fetch_field()) {
            if ($field->type == $mysqlLongType) {
                $shouldStoreResult = true;
            }

            if ($this->_nestJoin && $field->table != $this->_tableName) {
                $field->table = substr($field->table, \strlen(self::$prefix));
                $row[$field->table][$field->name] = null;
                $parameters[] = &$row[$field->table][$field->name];
            } else {
                $row[$field->name] = null;
                $parameters[] = &$row[$field->name];
            }
        }

        // avoid out of memory bug in php 5.2 and 5.3. Mysqli allocates lot of memory for long*
        // and blob* types. So to avoid out of memory issues store_result is used
        // https://github.com/joshcam/PHP-MySQLi-Database-Class/pull/119
        if ($shouldStoreResult) {
            $stmt->store_result();
        }

        \call_user_func_array([$stmt, 'bind_result'], $parameters);

        $this->totalCount = 0;
        $this->count = 0;

        while ($stmt->fetch()) {
            if ('object' == $this->returnType) {
                $result = new stdClass();
                foreach ($row as $key => $val) {
                    if (\is_array($val)) {
                        $result->{$key} = new stdClass();
                        foreach ($val as $k => $v) {
                            $result->{$key}->{$k} = $v;
                        }
                    } else {
                        $result->{$key} = $val;
                    }
                }
            } else {
                $result = [];
                foreach ($row as $key => $val) {
                    if (\is_array($val)) {
                        foreach ($val as $k => $v) {
                            $result[$key][$k] = $v;
                        }
                    } else {
                        $result[$key] = $val;
                    }
                }
            }
            ++$this->count;
            if ($this->_mapKey) {
                $results[$row[$this->_mapKey]] = \count($row) > 2 ? $result : end($result);
            } else {
                $results[] = $result;
            }
        }

        if ($shouldStoreResult) {
            $stmt->free_result();
        }

        $stmt->close();

        // stored procedures sometimes can return more then 1 resultset
        if ($this->mysqli()->more_results()) {
            $this->mysqli()->next_result();
        }

        if (\in_array('SQL_CALC_FOUND_ROWS', $this->_queryOptions)) {
            $stmt = $this->mysqli()->query('SELECT FOUND_ROWS()');
            $totalCount = $stmt->fetch_row();
            $this->totalCount = $totalCount[0];
        }

        if ('json' == $this->returnType) {
            return json_encode($results);
        }

        return $results;
    }

    /**
     * Abstraction method that will build an JOIN part of the query.
     */
    protected function _buildJoinOld()
    {
        if (empty($this->_join)) {
            return;
        }

        foreach ($this->_join as $data) {
            list($joinType, $joinTable, $joinCondition) = $data;

            if (\is_object($joinTable)) {
                $joinStr = $this->_buildPair('', $joinTable);
            } else {
                $joinStr = $joinTable;
            }

            $this->_query .= ' '.$joinType.' JOIN '.$joinStr.
                (false !== stripos($joinCondition, 'using') ? ' ' : ' on ')
                .$joinCondition;
        }
    }

    /**
     * Helper function to add variables into the query statement.
     *
     * @param array $tableData Variable with values
     *
     * @throws \Exception
     */
    protected function _buildOnDuplicate($tableData)
    {
        if (\is_array($this->_updateColumns) && !empty($this->_updateColumns)) {
            $this->_query .= ' ON DUPLICATE KEY UPDATE ';
            if ($this->_lastInsertId) {
                $this->_query .= $this->_lastInsertId.'=LAST_INSERT_ID ('.$this->_lastInsertId.'), ';
            }

            foreach ($this->_updateColumns as $key => $val) {
                // skip all params without a value
                if (is_numeric($key)) {
                    $this->_updateColumns[$val] = '';
                    unset($this->_updateColumns[$key]);
                } else {
                    $tableData[$key] = $val;
                }
            }
            $this->_buildDataPairs($tableData, array_keys($this->_updateColumns), false);
        }
    }

    /**
     * Abstraction method that will build an INSERT or UPDATE part of the query.
     *
     * @param array $tableData
     *
     * @throws \Exception
     */
    protected function _buildInsertQuery($tableData)
    {
        if (!\is_array($tableData)) {
            return;
        }

        $isInsert = preg_match('/^[INSERT|REPLACE]/', $this->_query);
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            if (isset($dataColumns[0])) {
                $this->_query .= ' (`'.implode('`, `', $dataColumns).'`) ';
            }
            $this->_query .= ' VALUES (';
        } else {
            $this->_query .= ' SET ';
        }

        $this->_buildDataPairs($tableData, $dataColumns, $isInsert);

        if ($isInsert) {
            $this->_query .= ')';
        }
    }

    /**
     * Abstraction method that will build the part of the WHERE conditions.
     *
     * @param string $operator
     * @param array  $conditions
     */
    protected function _buildCondition($operator, &$conditions)
    {
        if (empty($conditions)) {
            return;
        }

        // Prepare the where portion of the query
        $this->_query .= ' '.$operator;

        foreach ($conditions as $cond) {
            list($concat, $varName, $operator, $val) = $cond;
            $this->_query .= ' '.$concat.' '.$varName;

            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' '.$operator.' (';
                    if (\is_object($val)) {
                        $comparison .= $this->_buildPair('', $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',').' ) ';

                    break;

                case 'not between':
                case 'between':
                    $this->_query .= " {$operator} ? AND ? ";
                    $this->_bindParams($val);

                    break;

                case 'not exists':
                case 'exists':
                    $this->_query .= $operator.$this->_buildPair('', $val);

                    break;

                default:
                    if (\is_array($val)) {
                        $this->_bindParams($val);
                    } elseif (null === $val) {
                        $this->_query .= ' '.$operator.' NULL';
                    } elseif ('DBNULL' != $val || '0' == $val) {
                        $this->_query .= $this->_buildPair($operator, $val);
                    }
            }
        }
    }

    /**
     * Abstraction method that will build the GROUP BY part of the WHERE statement.
     */
    protected function _buildGroupBy()
    {
        if (empty($this->_groupBy)) {
            return;
        }

        $this->_query .= ' GROUP BY ';

        foreach ($this->_groupBy as $key => $value) {
            $this->_query .= $value.', ';
        }

        $this->_query = rtrim($this->_query, ', ').' ';
    }

    /**
     * Abstraction method that will build the LIMIT part of the WHERE statement.
     */
    protected function _buildOrderBy()
    {
        if (empty($this->_orderBy)) {
            return;
        }

        $this->_query .= ' ORDER BY ';
        foreach ($this->_orderBy as $prop => $value) {
            if ('rand()' == strtolower(str_replace(' ', '', $prop))) {
                $this->_query .= 'rand(), ';
            } else {
                $this->_query .= $prop.' '.$value.', ';
            }
        }

        $this->_query = rtrim($this->_query, ', ').' ';
    }

    /**
     * Abstraction method that will build the LIMIT part of the WHERE statement.
     *
     * @param array|int $numRows Array to define SQL limit in format Array ($offset, $count)
     *                           or only $count
     */
    protected function _buildLimit($numRows)
    {
        if (!isset($numRows)) {
            return;
        }

        if (\is_array($numRows)) {
            $this->_query .= ' LIMIT '.(int) $numRows[0].', '.(int) $numRows[1];
        } else {
            $this->_query .= ' LIMIT '.(int) $numRows;
        }
    }

    /**
     * Method attempts to prepare the SQL query
     * and throws an error if there was a problem.
     *
     * @return \mysqli_stmt
     *
     * @throws \Exception
     */
    protected function _prepareQuery()
    {
        $stmt = $this->mysqli()->prepare($this->_query);

        if (false !== $stmt) {
            if ($this->traceEnabled) {
                $this->traceStartQ = microtime(true);
            }

            return $stmt;
        }

        if (2006 === $this->mysqli()->errno && true === $this->autoReconnect && 0 === $this->autoReconnectCount) {
            $this->connect($this->defConnectionName);
            ++$this->autoReconnectCount;

            return $this->_prepareQuery();
        }

        $error = $this->mysqli()->error;
        $query = $this->_query;

        $errno = $this->mysqli()->errno;
        $this->reset();

        throw new \Exception(sprintf('%s query: %s', $error, $query), $errno);
    }

    /**
     * Referenced data array is required by mysqli since PHP 5.3+.
     *
     * @return array
     */
    protected function refValues(array &$arr)
    {
        // Reference in the function arguments are required for HHVM to work
        // https://github.com/facebook/hhvm/issues/5155
        // Referenced data array is required by mysqli since PHP 5.3+
        if (strnatcmp(\PHP_VERSION, '5.3') >= 0) {
            $refs = [];
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }

            return $refs;
        }

        return $arr;
    }

    /**
     * Function to replace ? with variables from bind variable.
     *
     * @param string $str
     * @param array  $vals
     *
     * @return string
     */
    protected function replacePlaceHolders($str, $vals)
    {
        $i = 1;
        $newStr = '';

        if (empty($vals)) {
            return $str;
        }

        while ($pos = strpos($str, '?')) {
            $val = $vals[$i++];
            if (\is_object($val)) {
                $val = '[object]';
            }
            if (null === $val) {
                $val = 'NULL';
            }
            $newStr .= substr($str, 0, $pos)."'".$val."'";
            $str = substr($str, $pos + 1);
        }
        $newStr .= $str;

        return $newStr;
    }

    /**
     * Abstraction method that will build an JOIN part of the query.
     */
    protected function _buildJoin()
    {
        if (empty($this->_join)) {
            return;
        }

        foreach ($this->_join as $data) {
            list($joinType, $joinTable, $joinCondition) = $data;

            if (\is_object($joinTable)) {
                $joinStr = $this->_buildPair('', $joinTable);
            } else {
                $joinStr = $joinTable;
            }

            $this->_query .= ' '.$joinType.' JOIN '.$joinStr.
                (false !== stripos($joinCondition, 'using') ? ' ' : ' on ')
                .$joinCondition;

            // Add join and query
            if (!empty($this->_joinAnd) && isset($this->_joinAnd[$joinStr])) {
                foreach ($this->_joinAnd[$joinStr] as $join_and_cond) {
                    list($concat, $varName, $operator, $val) = $join_and_cond;
                    $this->_query .= ' '.$concat.' '.$varName;
                    $this->conditionToSql($operator, $val);
                }
            }
        }
    }
}
