<?php
class Mage_Adminhtml_Offline_TextController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('ins_user');
		$this->_title("会员管理")->_title("会员设置");
		
		$this->_addContent($this->getLayout()->createBlock('adminhtml/ins_company', 'company'));
		$this->renderLayout();
	}
	public function listAction() {
		$this->_forward('index');
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$users = Mage::getModel('ins/company')->load($id);
		
		$this->loadLayout();
		
		$this->_setActiveMenu('ins_user');
		Mage::register('current_company',$users);
        $this->_addContent($this->getLayout()->createBlock('adminhtml/ins_company_edit', 'users'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/ins_company_edit_tabs'));
		$this->renderLayout();
	}
	public function saveAction() {
		$data = $this->getRequest()->getParams();
		$company = Mage::getModel('ins/company');
		if (isset($data['id']) && $data['id']) {
			$company->load($data['id']);
		}
	
		$company->addData($data);
		try {
			$company->save();
			//保存产品分类
			$cats = Mage::getResourceModel('ins/company_category_collection')
					->addFieldToFilter('company_id',$company->getId());
			
			$catOldIds = array();
			foreach ($cats as $_cat) {
				array_push($catOldIds,$_cat->getCategoryId());
			}
			$data['category_a'] = (array)$data['category_a'];
			$data['category_b'] = (array)$data['category_b'];
			
			$catData = array_merge($data['category_a'],(array)$data['category_b']);
			foreach ($catData as $_cid) {
				if ($_cid && !in_array($_cid,$catOldIds)) {
					//insert 
					$cats = Mage::getModel('ins/company_category')
						->setData('company_id',$company->getId())
						->setData('category_id',$_cid)
						->save();
				}
			}
			$catDelIds = array_diff($catOldIds,$catData);	
			$catDels = Mage::getResourceModel('ins/company_category_collection')
						->addFieldToFilter('company_id',$company->getId())
						->addFieldToFilter('category_id',array('in'=>$catDelIds));
			//var_dump($catDelIds);die();				
			foreach ($catDels as $_catDels) {
				$_delId = Mage::getModel('ins/company_category')->load($_catDels->getId());
				$_delId->delete();
			}
			//保存客户自定义属性
			$attr = $data['attr'];
			foreach ($attr as $_k=>$_v) {
				$value = Mage::getResourceModel('ins/company_attr_value_collection')
					->addFieldToFilter('attr_id',$_k)
					->addFieldToFilter('company_id',$company->getId())
					->getFirstItem();
				if (!$value->getId()) {
					$value->setAttrId($_k)
						->setCompanyId($company->getId());
				}
				$value->setValue($_v)->save();
			}
			$this->_getSession()->addSuccess('会员保存成功');
		} catch (Exception $e) {
			Mage::log('会员保存失败:#'.$company->getId(),false,'ins.log');
			$this->_getSession()->addError($e->getMessage());
		}
		if ($this->getRequest()->getParam('back')) {
    		$this->_redirect('*/*/edit',array('id'=>$company->getId()));
    	} else {
    		$this->_redirect('*/*/index');
    	}

	}
	
    public function deleteAction()
    {
    	$id = (int)$this->getRequest()->getParam('id');
    	$users = Mage::getModel('ins/users')->load($id);
    
    	try {
    		 $users->delete();
    		 $this->_getSession()->addSuccess("删除成功");
    	} catch(Exception $e) {
    		$this->_getSession()->addError();
    	}
    	$this->_redirect('*/*/index');
    }
	public function _isAllowed() {
    	return true;
	}
}