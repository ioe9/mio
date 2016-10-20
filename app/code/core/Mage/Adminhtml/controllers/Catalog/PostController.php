<?php
class Mage_Adminhtml_Catalog_PostController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('posts');
		$this->_title("文章管理")->_title("文章设置");
		$this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_post', 'post'));
		$this->renderLayout();
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function editAction() {
	
		$id = $this->getRequest()->getParam('id');
		$post = Mage::getModel('catalog/post')->load($id);
		
		$this->loadLayout();
		
		$this->_setActiveMenu('posts');
		Mage::register('current_post',$post);
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_post_edit', 'post'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_post_edit_tabs'));
		$this->renderLayout();
	}
	public function saveAction() {
		$data = $this->getRequest()->getParams();
		$post = Mage::getModel('catalog/post');
		if (isset($data['id']) && $data['id']) {
			$post->load($data['id']);
		}
	
		$post->addData($data);
		try {
			$post->save();
			$this->_getSession()->addSuccess('文章保存成功');
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		if ($this->getRequest()->getParam('back')) {
    		$this->_redirect('*/*/edit',array('id'=>$post->getId()));
    	} else {
    		$this->_redirect('*/*/index');
    	}

	}
	
    public function deleteAction()
    {
    	$id = (int)$this->getRequest()->getParam('id');
    	$post = Mage::getModel('catalog/post')->load($id);
    
    	try {
    		 $post->delete();
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
        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = Mage::getModel('catalog/post');
        $post->load($postId);
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->renderLayout();
    }
    public function categoriesJsonAction()
    {
        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = Mage::getModel('catalog/post');
        $post->load($postId);
        Mage::register('current_post', $post);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_post_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
