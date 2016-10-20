<?php
class Mage_Adminhtml_Block_Catalog_Category_Tab_Display extends Mage_Adminhtml_Block_Catalog_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Display Design'),'class' => 'fieldset-wide'));
        
        $fieldset->addField('display_slide', 'select', array(
            'name'      => 'display_slide',
            'label'     => "是否展示Slide",
            'title'     => "是否展示Slide",
            'required'  => false,
            'options'   => array('1'=>"展示",'0'=>'不展示'),
        ));
        $fieldset->addField('display_slide_block', 'select', array(
            'name'      => 'display_slide_block',
            'label'     => "Slide",
            'title'     => "Slide",
            'required'  => false,
			'options'   => Mage::getSingleton('cms/slide')->getSlideOptions(),
        ));

        $fieldset->addField('display_mode', 'select', array(
            'name'      => 'display_mode',
            'label'     => "展示方式",
            'title'     => "展示方式",
            'required'  => false,
            'options'   => array('POST'=>"纯文章",'PAGE'=>'CMS 静态块',"POST_AND_PAGE"=>"两者皆有"),
        ));
        $fieldset->addField('display_cms_block', 'select', array(
            'name'      => 'display_cms_block',
            'label'     => "CMS静态块",
            'title'     => "CMS静态块",
            'required'  => false,
            'options'   => Mage::getSingleton('cms/block')->getBlockOptions(),
        ));

       
        
        $form->addValues($this->getCategory()->getData());
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

}
