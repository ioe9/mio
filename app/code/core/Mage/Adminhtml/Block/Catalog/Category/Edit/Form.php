<?php
class Mage_Adminhtml_Block_Catalog_Category_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/edit/form.phtml');
    }

    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category

        $this->setChild('tabs',
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );

        // Save button
        if (!$category->isReadonly()) {
            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Save Category'),
                        'onclick'   => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                        'class' => 'save'
                    ))
            );
        }

        // Delete button
        if (!in_array($categoryId, $this->getRootIds()) && $category->isDeleteable()) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Delete Category'),
                        'onclick'   => "categoryDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$categoryId})",
                        'class' => 'delete'
                    ))
            );
        }

        // Reset button
        if (!$category->isReadonly()) {
            $resetPath = $categoryId ? '*/*/edit' : '*/*/add';
            $this->setChild('reset_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Reset'),
                        'onclick'   => "categoryReset('".$this->getUrl($resetPath, array('_current'=>true))."',true)"
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('save_button');
        }
        return '';
    }

    public function getResetButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('reset_button');
        }
        return '';
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild($alias . '_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config));
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }

        return $this;
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getHeader()
    {
        if ($this->hasStoreRootCategory()) {
            if ($this->getCategoryId()) {
                return $this->getCategoryName();
            } else {
                $parentId = (int) $this->getRequest()->getParam('parent');
                if ($parentId && ($parentId != Mage_Catalog_Model_Category::TREE_ROOT_ID)) {
                    return Mage::helper('catalog')->__('New Subcategory');
                } else {
                    return Mage::helper('catalog')->__('New Root Category');
                }
            }
        }
        return Mage::helper('catalog')->__('Set Root Category for Store');
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    public function getPostsJson()
    {
        $posts = $this->getCategory()->getPostsPosition();
        if (!empty($posts)) {
            return Mage::helper('core')->jsonEncode($posts);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
