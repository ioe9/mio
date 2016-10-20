<?php
class Mage_Customer_Model_Group extends Mage_Core_Model_Abstract
{
    /**
     * Xml config path for create account default group
     */
    const XML_PATH_DEFAULT_ID       = 'customer/create_account/default_group';

    const NOT_LOGGED_IN_ID          = 0;
    const CUST_GROUP_ALL            = 32000;

    const ENTITY                    = 'customer_group';

    const GROUP_CODE_MAX_LENGTH     = 32;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_group';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    protected function _construct()
    {
        $this->_init('customer/group');
    }

    /**
     * Alias for setCustomerGroupCode
     *
     * @param string $value
     */
    public function setCode($value)
    {
        return $this->setCustomerGroupCode($value);
    }

    /**
     * Alias for getCustomerGroupCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getCustomerGroupCode();
    }

    public function usesAsDefault()
    {
        $data = Mage::getConfig()->getStoresConfigByPath(self::XML_PATH_DEFAULT_ID);
        if (in_array($this->getId(), $data)) {
            return true;
        }
        return false;
    }

    /**
     * Prepare data before save
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->_prepareData();
        return parent::_beforeSave();
    }

    /**
     * Prepare customer group data
     *
     * @return Mage_Customer_Model_Group
     */
    protected function _prepareData()
    {
        $this->setCode(
            substr($this->getCode(), 0, self::GROUP_CODE_MAX_LENGTH)
        );
        return $this;
    }

}
