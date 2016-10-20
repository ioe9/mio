<?php
class Mage_Adminhtml_Block_Catalog_Category_Tab_Post extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_posts');
        $this->setDefaultSort('post_id');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('post_id', array('in'=>$postIds));
            }
            elseif(!empty($postIds)) {
                $this->getCollection()->addFieldToFilter('post_id', array('nin'=>$postIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_category'=>1));
        }
        $collection = Mage::getModel('catalog/post')->getCollection();
           
       
        $this->setCollection($collection);

        if ($this->getCategory()->getPostsReadonly()) {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            $this->getCollection()->addFieldToFilter('post_id', array('in'=>$postIds));
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getPostsReadonly()) {
            $this->addColumn('in_category', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_category',
                'values'    => $this->_getSelectedPosts(),
                'align'     => 'center',
                'index'     => 'post_id'
            ));
        }
        $this->addColumn('post_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'post_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'index'     => 'price'
        ));
        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'width'     => '1',
            'type'      => 'number',
            'index'     => 'position',
            'editable'  => !$this->getCategory()->getPostsReadonly()
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _getSelectedPosts()
    {
        $posts = $this->getRequest()->getPost('selected_posts');
        if (is_null($posts)) {
            $posts = $this->getCategory()->getPostsPosition();
            return array_keys($posts);
        }
        return $posts;
    }

}

