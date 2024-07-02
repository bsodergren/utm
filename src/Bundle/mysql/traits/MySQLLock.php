<?php
namespace UTM\Bundle\mysql\traits;


trait MySQLLock
{
    /**
     * Dynamic type list for temporary locking tables.
     *
     * @var array
     */
    protected $_tableLocks = [];

    /**
     * Variable which holds the current table lock method.
     *
     * @var string
     */
    protected $_tableLockMethod = 'READ';

    /**
     * LOCK IN SHARE MODE flag.
     *
     * @var bool
     */
    protected $_lockInShareMode = false;
    /**
     * Locks a table for R/W action.
     *
     * @author Jonas Barascu
     *
     * @param array|string $table The table to be locked. Can be a table or a view.
     *
     * @return bool if succeeded;
     *
     * @throws \Exception
     */
    public function lock($table)
    {
        // Main Query
        $this->_query = 'LOCK TABLES';

        // Is the table an array?
        if ('array' == \gettype($table)) {
            // Loop trough it and attach it to the query
            foreach ($table as $key => $value) {
                if ('string' == \gettype($value)) {
                    if ($key > 0) {
                        $this->_query .= ',';
                    }
                    $this->_query .= ' '.self::$prefix.$value.' '.$this->_tableLockMethod;
                }
            }
        } else {
            // Build the table prefix
            $table = self::$prefix.$table;

            // Build the query
            $this->_query = 'LOCK TABLES '.$table.' '.$this->_tableLockMethod;
        }

        // Execute the query unprepared because LOCK only works with unprepared statements.
        $result = $this->queryUnprepared($this->_query);
        $errno = $this->mysqli()->errno;

        // Reset the query
        $this->reset();

        // Are there rows modified?
        if ($result) {
            // Return true
            // We can't return ourself because if one table gets locked, all other ones get unlocked!
            return true;
        }
        // Something went wrong

        throw new \Exception('Locking of table '.$table.' failed', $errno);

        // Return the success value
        return false;
    }

    /**
     * Unlocks all tables in a database.
     * Also commits transactions.
     *
     * @author Jonas Barascu
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function unlock()
    {
        // Build the query
        $this->_query = 'UNLOCK TABLES';

        // Execute the query unprepared because UNLOCK and LOCK only works with unprepared statements.
        $result = $this->queryUnprepared($this->_query);
        $errno = $this->mysqli()->errno;

        // Reset the query
        $this->reset();

        // Are there rows modified?
        if ($result) {
            // return self
            return $this;
        }
        // Something went wrong

        throw new \Exception('Unlocking of tables failed', $errno);

        // Return self
        return $this;
    }

    
    /**
     * This method sets the current table lock method.
     *
     * @author Jonas Barascu
     *
     * @param string $method The table lock method. Can be READ or WRITE.
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function setLockMethod($method)
    {
        // Switch the uppercase string
        switch (strtoupper($method)) {
            // Is it READ or WRITE?
            case 'READ' || 'WRITE':
                // Succeed
                $this->_tableLockMethod = $method;

                break;

            default:
                // Else throw an exception
                throw new \Exception('Bad lock type: Can be either READ or WRITE');
                break;
        }

        return $this;
    }

}