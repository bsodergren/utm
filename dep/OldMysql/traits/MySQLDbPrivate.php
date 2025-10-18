<?php
namespace UTM\Bundle\mysql\traits;

trait MySQLDbPrivate
{
    
    /**
     * Pushes a unprepared statement to the mysqli stack.
     * WARNING: Use with caution.
     * This method does not escape strings by default so make sure you'll never use it in production.
     *
     * @author Jonas Barascu
     *
     * @param  [[Type]] $query [[Description]]
     *
     * @return bool|mysqli_result
     *
     * @throws \Exception
     */
    private function queryUnprepared($query)
    {
        // Execute query
        $stmt = $this->mysqli()->query($query);

        // Failed?
        if (false !== $stmt) {
            return $stmt;
        }

        if (2006 === $this->mysqli()->errno && true === $this->autoReconnect && 0 === $this->autoReconnectCount) {
            $this->connect($this->defConnectionName);
            ++$this->autoReconnectCount;

            return $this->queryUnprepared($query);
        }

        throw new \Exception(sprintf('Unprepared Query Failed, ERRNO: %u (%s)', $this->mysqli()->errno, $this->mysqli()->error), $this->mysqli()->errno);
    }

    /**
     * Internal function to build and execute INSERT/REPLACE calls.
     *
     * @param string $tableName  the name of the table
     * @param array  $insertData data containing information for inserting into the DB
     * @param string $operation  Type of operation (INSERT, REPLACE)
     *
     * @return bool boolean indicating whether the insert query was completed successfully
     *
     * @throws \Exception
     */
    private function _buildInsert($tableName, $insertData, $operation)
    {
        if ($this->isSubQuery) {
            return;
        }

        $this->_query = $operation.' '.implode(' ', $this->_queryOptions).' INTO '.self::$prefix.$tableName;
        $stmt = $this->_buildQuery(null, $insertData);
        $status = $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $haveOnDuplicate = !empty($this->_updateColumns);
        $this->reset();
        $this->count = $stmt->affected_rows;

        if ($stmt->affected_rows < 1) {
            // in case of onDuplicate() usage, if no rows were inserted
            if ($status && $haveOnDuplicate) {
                return true;
            }

            return false;
        }

        if ($stmt->insert_id > 0) {
            return $stmt->insert_id;
        }

        return true;
    }

    /**
     * Get where and what function was called for query stored in MysqliDB->trace.
     *
     * @return string with information
     */
    private function _traceGetCaller()
    {
        $dd = debug_backtrace();
        $caller = next($dd);
        while (isset($caller) && __FILE__ == $caller['file']) {
            $caller = next($dd);
        }

        return __CLASS__.'->'.$caller['function'].'() >>  file "'.
            str_replace($this->traceStripPrefix, '', $caller['file']).'" line #'.$caller['line'].' ';
    }

    /**
     * Convert a condition and value into the sql string.
     *
     * @param string       $operator The where constraint operator
     * @param array|string $val      The where constraint value
     */
    private function conditionToSql($operator, $val)
    {
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
                    $this->_query .= $operator.' NULL';
                } elseif ('DBNULL' != $val || '0' == $val) {
                    $this->_query .= $this->_buildPair($operator, $val);
                }
        }
    }
}