<?php
class Mage_Adminhtml_Block_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_withPostCount;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/tree.phtml');
        $this->setUseAjax(true);
        $this->_withPostCount = true;
    }

    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array(
            '_current'=>true,
            'id'=>null,
            '_query' => false
        ));

        $this->setChild('add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Subcategory'),
                    'onclick'   => "addNew('".$addUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_subcategory_button',
                    'style'     => $this->canAddSubCategory() ? '' : 'display: none;'
                ))
        );

        if ($this->canAddRootCategory()) {
            $this->setChild('add_root_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Add Root Category'),
                        'onclick'   => "addNew('".$addUrl."', true)",
                        'class'     => 'add',
                        'id'        => 'add_root_category_button'
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    public function getCategoryCollection()
    {
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->setLoadPostCount($this->_withPostCount);
            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true, 'id'=>null);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_category/jsonTree');
    }

    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_category/move');
    }

    public function getTree($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $categories = Mage::getResourceSingleton('catalog/category_tree')
           ->loadBreadcrumbsArray($path);
        if (empty($categories)) {
            return '';
        }
        foreach ($categories as $key => $category) {
            $categories[$key] = $this->_getNodeJson($category);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($categories) . ';'
            . ($this->canAddSubCategory()
                ? '$("add_subcategory_button").show();'
                : '$("add_subcategory_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'category_id', new Varien_Data_Tree);
        }
		
        $item = array();
        $item['text'] = $this->buildNodeName($node);

        $rootIds = in_array($node->getCategoryId(), $this->getRootIds());
	
        $item['id']  = $node->getId();

        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootIds) ? false : true);

        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Get category name
     *
     * @param Varien_Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        if ($this->_withPostCount) {
             $result .= ' (' . (int)$node->getPostCount() . ')';
        }
        return $result;
    }

    protected function _isCategoryMoveable($node)
    {
        $options = new Varien_Object(array(
            'is_moveable' => true,
            'category' => $node
        ));

        Mage::dispatchEvent('adminhtml_catalog_category_tree_is_moveable',
            array('options'=>$options)
        );

        return $options->getIsMoveable();
    }

    protected function _isParentSelectedCategory($node)
    {
    	
        if ($node && $this->getCategory()) {
            $pathIds = $this->getCategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function canAddRootCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'adminhtml_catalog_category_tree_can_add_root_category',
            array(
                'category' => $this->getCategory(),
                'options'   => $options,
            )
        );

        return $options->getIsAllow();
    }

    /**
     * Check availability of adding sub category
     *
     * @return boolean
     */
    public function canAddSubCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'adminhtml_catalog_category_tree_can_add_sub_category',
            array(
                'category' => $this->getCategory(),
                'options'   => $options,
            )
        );

        return $options->getIsAllow();
    }
}
