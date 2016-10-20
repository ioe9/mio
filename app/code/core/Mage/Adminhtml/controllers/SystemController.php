<?php
class Mage_Adminhtml_SystemController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'));
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            Mage::getSingleton('adminhtml/session')->setStoreId($storeId);
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system');
    }
}
