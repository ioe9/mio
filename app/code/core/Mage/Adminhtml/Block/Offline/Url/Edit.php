<?php
class Mage_Adminhtml_Block_Offline_Url_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
    public function __construct()
    {
        $this->_objectId   = 'urlid';
        $this->_controller = 'offline_url';
        $this->_mode = 'edit';
        parent::__construct();
       

    }
    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
		return '录入URL';
    }

}