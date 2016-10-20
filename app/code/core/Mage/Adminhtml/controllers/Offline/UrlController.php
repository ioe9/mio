<?php
class Mage_Adminhtml_Offline_UrlController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('ins_offline');
		$this->_title("URL录入");
		$this->_addContent($this->getLayout()->createBlock('adminhtml/offline_url', 'url'));
		$this->renderLayout();
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function editAction() {
		$this->loadLayout();
		$this->_setActiveMenu('ins_offline');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/offline_url_edit', 'url.edit'));
		$this->renderLayout();
	}
	
	/**
	 * 录入URL
	 * 0.URL合法性检查
	 * 1.去重、去掉已入库的URL，ins_url 总url表 统一带二级域名，默认是WWW
	 * 1-1.线上客户输入的新URL，单独试用PHP脚本按小时同步到本地
	 * 1-2.如果非客户自有网站，需要合并，做同义转换 ins_url_same
	 * 
	 */
	public function saveAction() {
		$data = $this->getRequest()->getParams();
		$urls = preg_replace("/http[s]*:\/\//i",'',$data['urls']);
		$urlArr = explode("\n",$urls);

		$coreResource = Mage::getSingleton('core/resource');
		$conn = $coreResource->getConnection('core_write');
		
		
		foreach ($urlArr as $_url) {
			if ($_url && true) { //验证URL合法项
				//TODO... 添加识别二级域名的方法，没有二级域名则加 www
				$tempArr = explode('.',$_url);
				$count = count($tempArr);
				if ($count >= 2) {
					if ($count==2) {
						$_url = 'www.'.$_url; //后期改用Whois Helper类来处理
					}
					$temp = explode('/',$_url);
					$_url = $temp[0];
					//大站的主页，放到Same表里，大站列表，供过滤
					$sql = "select url_id from ins_url where url='$_url'";
					$resource = $conn->query($sql);
					$res = $resource->fetch($resource);
					if (!$res || !$res['url_id']) {
						//不存在，可以入库
						$sqlInsert = "insert into ins_url (`url`) values ('$_url')";
						$res = $conn->query($sqlInsert);
						$this->_getSession()->addError($_url);
					}
				}
				
			}
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