<?php
class Mage_Adminhtml_Block_Catalog_Category_Tab_General extends Mage_Adminhtml_Block_Catalog_Form
{

    protected $_category;

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
        $form->setHtmlIdPrefix('_general');
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('General Information'),'class' => 'fieldset-wide',));

        if (!$this->getCategory()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $parentId
            ));
        } else {
            $fieldset->addField('id', 'hidden', array(
                'name'  => 'id',
                'value' => $this->getCategory()->getId()
            ));
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $this->getCategory()->getPath()
            ));
        }
		
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => "名称",
            'title'     => "名称",
            'required'  => true,
        ));
        
        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => "URL Key",
            'title'     => "URL Key",
            'required'  => true,
        ));
        $fieldset->addField('include_in_menu', 'select', array(
            'name'      => 'include_in_menu',
            'label'     => "加入主菜单",
            'title'     => "加入主菜单",
            'required'  => false,
            'options'   => array('0'=>"否",'1'=>'是'),
        ));
        $fieldset->addField('is_active', 'select', array(
            'name'      => 'is_active',
            'label'     => "状态",
            'title'     => "状态",
            'required'  => false,
            'options'   => array('0'=>"禁用",'1'=>'启用'),
        ));

        $fieldset->addField('thumbnail', 'image', array(
            'name'      => 'thumbnail',
            'label'     => "小图",
            'title'     => "小图",
            'required'  => false,
        ));
        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => "大图",
            'title'     => "大图",
            'required'  => false,
        ));
        $fieldset->addField('sdesc', 'text', array(
            'name'      => 'sdesc',
            'label'     => '简要描述',
            'title'     => '简要描述',
            'required'  => false,
            
        ));
        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => '详细描述',
            'title'     => '详细描述',
            'style'     => 'height:36em',
            'required'  => false,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig()
            
        ));
        $fieldset->addField('meta_title', 'textarea', array(
            'name'      => 'meta_title',
            'label'     => "Meta Title",
            'title'     => "Meta Title",
            'required'  => false,
        ));
		$fieldset->addField('meta_keywords', 'textarea', array(
            'name'      => 'meta_keywords',
            'label'     => "Meta Keywords",
            'title'     => "Meta Keywords",
            'required'  => false,
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'name'      => 'meta_description',
            'label'     => "Meta Description",
            'title'     => "Meta Description",
            'required'  => false,
        ));
		

        if ($this->getCategory()->getId()) {
            if ($this->getCategory()->getLevel() == 1) {
                $fieldset->removeField('url_key');
                $fieldset->addField('url_key', 'hidden', array(
                    'name'  => 'url_key',
                    'value' => $this->getCategory()->getUrlKey()
                ));
            }
        }

        $form->addValues($this->getCategory()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_category_helper_image')
        );
    }

    protected function _getParentCategoryOptions($node=null, &$options=array())
    {
        if (is_null($node)) {
            $node = $this->getRoot();
        }

        if ($node) {
            $options[] = array(
               'value' => $node->getPathId(),
               'label' => str_repeat('&nbsp;', max(0, 3*($node->getLevel()))) . $this->escapeHtml($node->getName()),
            );

            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }

}

