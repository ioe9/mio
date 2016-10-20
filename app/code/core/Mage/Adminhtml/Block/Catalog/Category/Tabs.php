<?php
class Mage_Adminhtml_Block_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Initialize Tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Retrieve cattegory object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return Adminhtml Catalog Helper
     *
     * @return Mage_Adminhtml_Helper_Catalog
     */
    public function getCatalogHelper()
    {
        return Mage::helper('adminhtml/catalog');
    }

    /**
     * Prepare Layout Content
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tabs
     */
    protected function _prepareLayout()
    {
       
		$this->addTab('general', array(
            'label'     => "基本信息",
            'content'   => $this->getLayout()->createBlock(
                'adminhtml/catalog_category_tab_general',
                'category.general'
            )->toHtml(),
        ));
        $this->addTab('display', array(
            'label'     => "展示设置",
            'content'   => $this->getLayout()->createBlock(
                'adminhtml/catalog_category_tab_display',
                'category.display'
            )->toHtml(),
        ));
        $this->addTab('design', array(
            'label'     => "模版设置",
            'content'   => $this->getLayout()->createBlock(
                'adminhtml/catalog_category_tab_design',
                'category.design'
            )->toHtml(),
        ));
        $this->addTab('posts', array(
            'label'     => "分类文章",
            'content'   => $this->getLayout()->createBlock(
                'adminhtml/catalog_category_tab_post',
                'category.post.grid'
            )->toHtml(),
        ));


        Mage::dispatchEvent('adminhtml_catalog_category_tabs', array(
            'tabs'  => $this
        ));

        /*$this->addTab('features', array(
            'label'     => Mage::helper('catalog')->__('Feature Posts'),
            'content'   => 'Feature Posts'
        ));        */
        return parent::_prepareLayout();
    }
}
