<?php
class Mage_Adminhtml_Block_Offline_Url extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'offline_url';
       	$this->_addButtonLabel = "录入URL";
       	parent::__construct();
   		$this->_headerText = "URL列表"; 
    }
}
