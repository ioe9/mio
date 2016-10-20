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


class Mage_Catalog_Block_Post extends Mage_Core_Block_Template
{
    protected $_finalPrice = array();

    public function getPost()
    {
        if (!$this->getData('post') instanceof Mage_Catalog_Model_Post) {
            if ($this->getData('post')->getPostId()) {
                $postId = $this->getData('post')->getPostId();
            }
            if ($postId) {
                $post = Mage::getModel('catalog/post')->load($postId);
                if ($post) {
                    $this->setPost($post);
                }
            }
        }
        return $this->getData('post');
    }

    public function getPrice()
    {
        return $this->getPost()->getPrice();
    }

    public function getFinalPrice()
    {
        if (!isset($this->_finalPrice[$this->getPost()->getId()])) {
            $this->_finalPrice[$this->getPost()->getId()] = $this->getPost()->getFinalPrice();
        }
        return $this->_finalPrice[$this->getPost()->getId()];
    }

    public function getPriceHtml($post)
    {
        $this->setTemplate('catalog/post/price.phtml');
        $this->setPost($post);
        return $this->toHtml();
    }
}
