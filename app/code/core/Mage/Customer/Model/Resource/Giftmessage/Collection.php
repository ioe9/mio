<?php
class Mage_Customer_Model_Resource_Giftmessage_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('customer/giftmessage');
    }
}
