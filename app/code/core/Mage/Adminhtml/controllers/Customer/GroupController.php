<?php
class Mage_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Controller_Action
{
    protected function _initGroup()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Customer Groups'));

        Mage::register('current_group', Mage::getModel('customer/group'));
        $groupId = $this->getRequest()->getParam('id');
        if (!is_null($groupId)) {
            Mage::registry('current_group')->load($groupId);
        }

    }
    /**
     * Customer groups list.
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Customer Groups'));

        $this->loadLayout();
        
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customer Groups'), Mage::helper('customer')->__('Customer Groups'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/customer_group', 'customer_group'));
        $this->renderLayout();
    }

    /**
     * Edit or create customer group.
     */
    public function newAction()
    {
        $this->_initGroup();
        $this->loadLayout();
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customer Groups'), Mage::helper('customer')->__('Customer Groups'), $this->getUrl('*/customer_group'));

        $currentGroup = Mage::registry('current_group');

        if (!is_null($currentGroup->getId())) {
            $this->_addBreadcrumb(Mage::helper('customer')->__('Edit Group'), Mage::helper('customer')->__('Edit Customer Groups'));
        } else {
            $this->_addBreadcrumb(Mage::helper('customer')->__('New Group'), Mage::helper('customer')->__('New Customer Groups'));
        }

        $this->_title($currentGroup->getId() ? $currentGroup->getCode() : $this->__('New Group'));

        $this->getLayout()->getBlock('content')
            ->append($this->getLayout()->createBlock('adminhtml/customer_group_edit', 'group')
                        ->setEditMode((bool)Mage::registry('current_group')->getId()));

        $this->renderLayout();
    }

    /**
     * Edit customer group action. Forward to new action.
     */
    public function editAction()
    {
        $this->_forward('new');
    }

    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }

        try {
            $customerGroupCode = (string)$this->getRequest()->getParam('code');

            if (!empty($customerGroupCode)) {
                $customerGroup->setCode($customerGroupCode);
            }

            $customerGroup->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
            $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
            $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
            return;
        }

    }

    /**
     * Delete customer group action
     */
    public function deleteAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $customerGroup->load($id);
                $customerGroup->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
                return;
            }
        }

        $this->_redirect('*/customer_group');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/group');
    }
}
