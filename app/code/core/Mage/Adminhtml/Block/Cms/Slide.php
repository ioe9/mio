<?php
class Mage_Adminhtml_Block_Cms_Slide extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'cms_slide';
        $this->_headerText = Mage::helper('cms')->__('Slide');
        $this->_addButtonLabel = Mage::helper('cms')->__('Add New Slide');
        parent::__construct();
    }

}
