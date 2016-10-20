<?php
class Magento_Db_Adapter_Pdo_Mysql extends Varien_Db_Adapter_Pdo_Mysql
{
    /**
     * Returns flag is transaction now?
     *
     * @return bool
     */
    public function isTransaction()
    {
        return (bool)$this->_transactionLevel;
    }

    /**
     * Batched insert of specified select
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param array $fields
     * @param bool $mode
     * @param int $step
     * @return int
     */
    public function insertBatchFromSelect(Varien_Db_Select $select, $table, array $fields = array(),
                                          $mode = false, $step = 10000
    ) {
        $limitOffset = 0;
        $totalAffectedRows = 0;

        do {
            $select->limit($step, $limitOffset);
            $result = $this->query(
                $this->insertFromSelect($select, $table, $fields, $mode)
            );

            $affectedRows = $result->rowCount();
            $totalAffectedRows += $affectedRows;
            $limitOffset += $step;
        } while ($affectedRows > 0);

        return $totalAffectedRows;
    }

    /**
     * Retrieve bunch of queries for specified select splitted by specified step
     *
     * @param Varien_Db_Select $select
     * @param string $entityIdField
     * @param int $step
     * @return array
     */
    public function splitSelect(Varien_Db_Select $select, $entityIdField = '*', $step = 10000)
    {
        $countSelect = clone $select;

        $countSelect->reset(Magento_Db_Select::COLUMNS);
        $countSelect->reset(Magento_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Magento_Db_Select::LIMIT_OFFSET);
        $countSelect->columns('COUNT(' . $entityIdField . ')');

        $row = $this->fetchRow($countSelect);
        $totalRows = array_shift($row);

        $bunches = array();
        for ($i = 0; $i <= $totalRows; $i += $step) {
            $bunchSelect = clone $select;
            $bunches[] = $bunchSelect->limit($step, $i);
        }

        return $bunches;
    }

    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        if (is_float($value)) {
            $value = $this->_convertFloat($value);
            return $value;
        }

        return parent::_quote($value);
    }

    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quote
     * and then returned as a comma-separated string.
     *
     * @param mixed $value The value to quote.
     * @param null $type OPTIONAL the SQL datatype name, or constant, or null.
     * @return mixed|string An SQL-safe quoted value (or string of separated values).
     */
    public function quote($value, $type = null)
    {
        $this->_connect();

        if ($type !== null &&
            array_key_exists($type = strtoupper($type), $this->_numericDataTypes) &&
            $this->_numericDataTypes[$type] == Varien_Db::FLOAT_TYPE) {
                $value = $this->_convertFloat($value);
                $quoteValue = sprintf('%F', $value);
                return $quoteValue;
        } elseif (is_float($value)) {
            return $this->_quote($value);
        }

        return parent::quote($value, $type);
    }

    /**
     * Convert float values that are not supported by MySQL to alternative representation value.
     * Value 99999999.9999 is a maximum value that may be stored in Magento decimal columns in DB.
     *
     * @param float $value
     * @return float
     */
    protected function _convertFloat($value)
    {
        $value = (float) $value;

        if (is_infinite($value)) {
            $value = ($value > 0)
                ? 99999999.9999
                : -99999999.9999;
        } elseif (is_nan($value)) {
            $value = 0.0;
        }

        return $value;
    }
}