<?php
class Mage_Adminhtml_Block_Catalog_Post_Edit_Tab_Design extends Mage_Adminhtml_Block_Catalog_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPost()
    {
        if (!$this->_post) {
            $this->_post = Mage::registry('current_post');
        }
        return $this->_post;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setDataObject($this->getPost());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Custom Design'),'class' => 'fieldset-wide'));
        
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
        $form->addValues($this->getPost()->getData());

        $this->setForm($form);
    }

}
