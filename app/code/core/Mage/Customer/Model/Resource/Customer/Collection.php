<?php
class Mage_Customer_Model_Resource_Customer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer/customer');
    }
}
