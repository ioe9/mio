<?php
class Mage_Adminhtml_Block_Catalog_Category_Tab_Design extends Mage_Adminhtml_Block_Catalog_Form
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

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Custom Design'),'class' => 'fieldset-wide'));
        
        $fieldset->addField('custom_use_parent_settings', 'select', array(
            'name'      => 'custom_use_parent_settings',
            'label'     => "使用父分类页面布局",
            'title'     => "使用父分类页面布局",
            'required'  => false,
            'options'   => array('0'=>"否",'1'=>'是'),
        ));
        $fieldset->addField('custom_apply_to_posts', 'select', array(
            'name'      => 'custom_apply_to_posts',
            'label'     => "应用到分类下的文章",
            'title'     => "应用到分类下的文章",
            'required'  => false,
            'options'   => array('0'=>"否",'1'=>'是'),
        ));
        $designOptions = Mage::getSingleton('core/design_source_design')->getAllOptions();
        //array_unshift($designOptions, array('value'=>'', 'label'=>Mage::helper('catalog')->__('No layout updates')));
        //echo "<xmp>";var_dump($designOptions);echo "</xmp>";
        $designOptionsNew = array();
        foreach ($designOptions as $_design) {
        	$designOptionsNew[$_design['value']] = $_design['label'];
        }
        $fieldset->addField('custom_design', 'select', array(
            'name'      => 'custom_design',
            'label'     => "自定义主题",
            'title'     => "自定义主题",
            'required'  => false,
            'values'   => $designOptions,
        ));
        $pageLayoutOptions = Mage::getSingleton('page/source_layout')->toOptionArray();
        array_unshift($pageLayoutOptions, array('value'=>'', 'label'=>Mage::helper('catalog')->__('No layout updates')));
        $pageLayoutOptionsNew = array();
        foreach ($pageLayoutOptions as $_layout) {
        	$pageLayoutOptionsNew[$_layout['value']] = $_layout['label'];
        }
        $fieldset->addField('page_layout', 'select', array(
            'name'      => 'page_layout',
            'label'     => "页面布局",
            'title'     => "页面布局",
            'required'  => false,
            'options'   => $pageLayoutOptionsNew,
        ));
		$fieldset->addField('custom_layout_update', 'textarea', array(
            'name'      => 'custom_layout_update',
            'label'     => "XML布局更新",
            'title'     => "XML布局更新",
            'required'  => false,
        ));
        $form->addValues($this->getCategory()->getData());
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

}
