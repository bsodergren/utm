<?php
namespace UTM\Bundle\mysql\traits;


trait MySQLDbTransaction
{
    protected $_transaction_in_progress = false;

    /**
     * Begin a transaction.
     *
     * @uses mysqli->autocommit(false)
     * @uses register_shutdown_function(array($this, "_transaction_shutdown_check"))
     *
     * @throws \Exception
     */
    public function startTransaction()
    {
        $this->mysqli()->autocommit(false);
        $this->_transaction_in_progress = true;
        register_shutdown_function([$this, '_transaction_status_check']);
    }

    /**
     * Transaction commit.
     *
     * @uses mysqli->commit();
     * @uses mysqli->autocommit(true);
     *
     * @throws \Exception
     */
    public function commit()
    {
        $result = $this->mysqli()->commit();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);

        return $result;
    }

    /**
     * Transaction rollback function.
     *
     * @uses mysqli->rollback();
     * @uses mysqli->autocommit(true);
     *
     * @throws \Exception
     */
    public function rollback()
    {
        $result = $this->mysqli()->rollback();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);

        return $result;
    }

    /**
     * Shutdown handler to rollback uncommited operations in order to keep
     * atomic operations sane.
     *
     * @uses mysqli->rollback();
     *
     * @throws \Exception
     */
    public function _transaction_status_check()
    {
        if (!$this->_transaction_in_progress) {
            return;
        }
        $this->rollback();
    }
}