<?php
class Mage_Adminhtml_Block_Catalog_Event_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('event_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('活动基本信息');
    }


    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => '基本信息',
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_event_edit_tab_form')->toHtml(),
        ));
		$this->addTab('categories', array(
            'label'     => "分类设置",
            'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
            'class'     => 'ajax',
        ));
        $this->addTab('seo', array(
            'label'     => "SEO设置",
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_event_edit_tab_seo')->toHtml(),
        ));
        $this->addTab('design', array(
            'label'     => "模板设置",
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_event_edit_tab_design')->toHtml(),
        ));
        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }
    
    protected function _updateActiveTab()
    {
    	$tabId = $this->getRequest()->getParam('tab');
    	if ($tabId) {
    		$tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
    		if ($tabId) {
    			$this->setActiveTab($tabId);
    		}
    	}
    	else {
    	   $this->setActiveTab('general'); 
    	}
    }
}
