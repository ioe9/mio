<?php
class Mage_Adminhtml_Block_Catalog_Post_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
    public function __construct()
    {
        $this->_objectId   = 'postid';
        $this->_controller = 'catalog_post';
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
    	$post = Mage::registry('current_post');
        if (Mage::registry('current_post')->getId()) {
            return "编辑文章:".$this->escapeHtml(Mage::registry('current_post')->getTitle());
        } else {
    		return '新建文章';
        }
    }

}