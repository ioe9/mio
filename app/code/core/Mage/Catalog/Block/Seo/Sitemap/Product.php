<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SEO Posts Sitemap block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Seo_Sitemap_Post extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize posts collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $collection = Mage::getModel('catalog/post')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Post_Collection */

        $collection->addFieldToSelect('name');
        $collection->addFieldToSelect('url_key');
        $collection->addStoreFilter();

        Mage::getSingleton('catalog/post_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/post_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->setCollection($collection);

        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Post $category
     * @return string
     */
    public function getItemUrl($post)
    {
        $helper = Mage::helper('catalog/post');
        /* @var $helper Mage_Catalog_Helper_Post */
        return $helper->getPostUrl($post);
    }

}
