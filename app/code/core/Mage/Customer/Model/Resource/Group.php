<?php
class Mage_Customer_Model_Resource_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer/group', 'customer_group_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Customer_Model_Resource_Group
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'customer_group_code',
                'title' => Mage::helper('customer')->__('Customer Group')
            ));

        return $this;
    }

    /**
     * Check if group uses as default
     *
     * @param  Mage_Core_Model_Abstract $group
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $group)
    {
        if ($group->usesAsDefault()) {
            Mage::throwException(Mage::helper('customer')->__('The group "%s" cannot be deleted', $group->getCode()));
        }
        return parent::_beforeDelete($group);
    }

    /**
     * Method set default group id to the customers collection
     *
     * @param Mage_Core_Model_Abstract $group
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $group)
    {
        $customerCollection = Mage::getResourceModel('customer/customer_collection')
            ->addFieldToFilter('group_id', $group->getId())
            ->load();
        foreach ($customerCollection as $customer) {
            $defaultGroupId = Mage::helper('customer')->getDefaultCustomerGroupId($customer->getStoreId());
            $customer->setGroupId($defaultGroupId);
            $customer->save();
        }
        return parent::_afterDelete($group);
    }
}
