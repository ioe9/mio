<?php
class Mage_Adminhtml_Block_Catalog_Event extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_event';
       
       	$this->_addButtonLabel = "新建活动";
       	parent::__construct();
   		$this->_headerText = "活动管理";
       	
    }
}
