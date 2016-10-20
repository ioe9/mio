<?php
class Mage_Adminhtml_Catalog_EventController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('events');
		$this->_title("活动管理")->_title("活动设置");
		
		$this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_event', 'event'));
		$this->renderLayout();
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function editAction() {
	
		$id = $this->getRequest()->getParam('id');
		$event = Mage::getModel('catalog/event')->load($id);
		
		$this->loadLayout();
		
		$this->_setActiveMenu('events');
		Mage::register('current_event',$event);
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_event_edit', 'event'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_event_edit_tabs'));
		$this->renderLayout();
	}
	public function saveAction() {
		$data = $this->getRequest()->getParams();
		$event = Mage::getModel('catalog/event');
		if (isset($data['id']) && $data['id']) {
			$event->load($data['id']);
		}
	
		$event->addData($data);
		try {
			$event->save();
			$this->_getSession()->addSuccess('活动保存成功');
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		if ($this->getRequest()->getParam('back')) {
    		$this->_redirect('*/*/edit',array('id'=>$event->getId()));
    	} else {
    		$this->_redirect('*/*/index');
    	}

	}
	
    public function deleteAction()
    {
    	$id = (int)$this->getRequest()->getParam('id');
    	$event = Mage::getModel('catalog/event')->load($id);
    
    	try {
    		 $event->delete();
    		 $this->_getSession()->addSuccess("删除成功");
    	} catch(Exception $e) {
    		$this->_getSession()->addError();
    	}
    	$this->_redirect('*/*/index');
    }
	public function _isAllowed() {
    	return true;
	}
    public function categoriesAction()
    {
        $eventId  = (int) $this->getRequest()->getParam('id');
        $event    = Mage::getModel('catalog/event');
        $event->load($eventId);
        Mage::register('current_event', $event);
        $this->loadLayout();
        $this->renderLayout();
    }
    public function categoriesJsonAction()
    {
        $eventId  = (int) $this->getRequest()->getParam('id');
        $event    = Mage::getModel('catalog/event');
        $event->load($eventId);
        Mage::register('current_event', $event);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_event_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
