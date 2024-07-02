<?php

namespace UTM\Bundle\mysql\traits;

trait MySQLDbSet
{
    /**
     * Method to set a prefix.
     *
     * @param string $prefix Contains a table prefix
     *
     * @return MysqliDb
     */
    public function setPrefix($prefix = '')
    {
        self::$prefix = $prefix;

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) options for SQL queries.
     *
     * @uses $MySqliDb->setQueryOption('name');
     *
     * @param array|string $options the options name of the query
     *
     * @return MysqliDb
     *
     * @throws \Exception
     */
    public function setQueryOption($options)
    {
        $allowedOptions = [
            'ALL', 'DISTINCT', 'DISTINCTROW', 'HIGH_PRIORITY', 'STRAIGHT_JOIN', 'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS',
            'LOW_PRIORITY', 'IGNORE', 'QUICK', 'MYSQLI_NESTJOIN', 'FOR UPDATE', 'LOCK IN SHARE MODE',
        ];

        if (!\is_array($options)) {
            $options = [$options];
        }

        foreach ($options as $option) {
            $option = strtoupper($option);
            if (!\in_array($option, $allowedOptions)) {
                throw new \Exception('Wrong query option: '.$option);
            }

            if ('MYSQLI_NESTJOIN' == $option) {
                $this->_nestJoin = true;
            } elseif ('FOR UPDATE' == $option) {
                $this->_forUpdate = true;
            } elseif ('LOCK IN SHARE MODE' == $option) {
                $this->_lockInShareMode = true;
            } else {
                $this->_queryOptions[] = $option;
            }
        }

        return $this;
    }

    /**
     * Query execution time tracking switch.
     *
     * @param bool   $enabled     Enable execution time tracking
     * @param string $stripPrefix Prefix to strip from the path in exec log
     *
     * @return MysqliDb
     */
    public function setTrace($enabled, $stripPrefix = null)
    {
        $this->traceEnabled = $enabled;
        $this->traceStripPrefix = $stripPrefix;

        return $this;
    }
}
