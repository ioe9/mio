<?php
class Mage_Adminhtml_Block_Catalog_Post extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_post';
       
       	$this->_addButtonLabel = "新建文章";
       	parent::__construct();
       	
       
       		$this->_headerText = "文章管理";
       	
    }
}
