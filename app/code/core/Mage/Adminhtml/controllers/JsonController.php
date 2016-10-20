<?php
class Mage_Adminhtml_JsonController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return JSON-encoded array of country regions
     *
     * @return string
     */
    public function countryRegionAction()
    {
        $arrRes = array();

        $countryId = $this->getRequest()->getParam('parent');
        $arrRegions = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->toOptionArray();

        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrRes));
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
