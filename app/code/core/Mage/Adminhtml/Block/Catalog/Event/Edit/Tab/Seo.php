<?php
class Mage_Adminhtml_Block_Catalog_Event_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Define Form settings
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
		$model = Mage::registry('current_event');
		$data = $model->getData();
        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => 'SEO设置',
            'class'    => 'fieldset-wide',
        ));
       	$fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => 'URL Key',
            'title'     => 'URL Key',
            'required'  => false,
        ));
        $fieldset->addField('meta_title', 'text', array(
            'name'      => 'meta_title',
            'label'     => 'Meta Title',
            'title'     => 'Meta Title',
            'required'  => false,
        ));
        $fieldset->addField('meta_keywords', 'textarea', array(
            'name'      => 'meta_keywords',
            'label'     => 'Meta Keywords',
            'title'     => 'Meta Keywords',
            'required'  => false,
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'name'      => 'meta_description',
            'label'     => 'Meta Description',
            'title'     => 'Meta Description',
            'required'  => false,
        ));
        
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
