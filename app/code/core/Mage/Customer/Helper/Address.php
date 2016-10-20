<?php
class Mage_Customer_Helper_Address extends Mage_Core_Helper_Abstract
{
    /**
     * VAT Validation parameters XML paths
     */
    const XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT = 'customer/create_account/viv_disable_auto_group_assign_default';
    const XML_PATH_VIV_ON_EACH_TRANSACTION         = 'customer/create_account/viv_on_each_transaction';
    const XML_PATH_VAT_VALIDATION_ENABLED          = 'customer/create_account/auto_group_assign';
    const XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE = 'customer/create_account/tax_calculation_address_type';
    const XML_PATH_VAT_FRONTEND_VISIBILITY = 'customer/create_account/vat_frontend_visibility';

    /**
     * Array of Customer Address Attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Customer address config node per website
     *
     * @var array
     */
    protected $_config          = array();

    /**
     * Customer Number of Lines in a Street Address per website
     *
     * @var array
     */
    protected $_streetLines     = array();
    protected $_formatTemplate  = array();

    public function getRenderer($renderer)
    {
        if(is_string($renderer) && $className = Mage::getConfig()->getBlockClassName($renderer)) {
            return new $className();
        } else {
            return $renderer;
        }
    }

    public function getFormat($code)
    {
        $format = Mage::getSingleton('customer/address_config')->getFormatByCode($code);
        return $format->getRenderer() ? $format->getRenderer()->getFormat() : '';
    }

    /**
     * Determine if specified address config value can be shown
     *
     * @param string $key
     * @return bool
     */
    public function canShowConfig($key)
    {
        return (bool)$this->getConfig($key);
    }

    /**
     * Retrieve disable auto group assign default value
     *
     * @return bool
     */
    public function getDisableAutoGroupAssignDefaultValue()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT);
    }

    /**
     * Retrieve 'validate on each transaction' value
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function getValidateOnEachTransaction($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VIV_ON_EACH_TRANSACTION, $store);
    }

}
