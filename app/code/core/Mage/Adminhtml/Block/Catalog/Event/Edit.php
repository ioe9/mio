<?php
class Mage_Adminhtml_Block_Catalog_Event_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
    public function __construct()
    {
        $this->_objectId   = 'eventid';
        $this->_controller = 'catalog_event';
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        parent::__construct();
       

    }
    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
    	$event = Mage::registry('current_event');
        if (Mage::registry('current_event')->getId()) {
            return "编辑活动:".$this->escapeHtml(Mage::registry('current_event')->getTitle());
        } else {
    		return '新建活动';
        }
    }

}