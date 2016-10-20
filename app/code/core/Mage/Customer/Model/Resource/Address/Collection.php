<?php
class Mage_Customer_Model_Resource_Address_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer/address');
    }

    /**
     * Set customer filter
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Resource_Address_Collection
     */
    public function setCustomerFilter($customer)
    {
        if ($customer->getId()) {
            $this->addFieldToFilter('parent_id', $customer->getId());
        }
        return $this;
    }
}
