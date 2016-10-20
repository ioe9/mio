<?php
class Mage_Customer_Model_Resource_Address extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/address', 'address_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        return $this;
    }
    
    /**
     * Set default shipping to address
     *
     * @param Varien_Object $address
     * @return Mage_Customer_Model_Resource_Address
     */
    protected function _afterSave(Varien_Object $address)
    {
        if ($address->getIsCustomerSaveTransaction()) {
            return $this;
        }
        if ($address->getId() && ($address->getIsDefaultShipping())) {
            $customer = Mage::getModel('customer/customer')
                ->load($address->getCustomerId());
            if ($address->getIsDefaultShipping()) {
                $customer->setDefaultShipping($address->getId());
            }
            $customer->save();
        }
        return $this;
    }

    /**
     * Return customer id
     * @deprecated
     *
     * @param Mage_Customer_Model_Address $object
     * @return int
     */
    public function getCustomerId($object)
    {
        return $object->getData('customer_id') ? $object->getData('customer_id') : $object->getParentId();
    }

    /**
     * Set customer id
     * @deprecated
     *
     * @param Mage_Customer_Model_Address $object
     * @param int $id
     * @return Mage_Customer_Model_Address
     */
    public function setCustomerId($object, $id)
    {
        return $this;
    }
}
