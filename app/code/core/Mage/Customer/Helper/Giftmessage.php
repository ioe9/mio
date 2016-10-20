<?php
class Mage_Customer_Helper_Giftmessage extends Mage_Core_Helper_Data
{
    /**
     * Giftmessages allow section in configuration
     *
     */
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS = 'sales/gift_options/allow_items';
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER = 'sales/gift_options/allow_order';

    /**
     * Next id for edit gift message block
     *
     * @var integer
     */
    protected $_nextId = 0;

    /**
     * Inner cache
     *
     * @var array
     */
    protected $_innerCache = array();

    /**
     * Retrive old stule edit button html for editing of giftmessage in popup
     *
     * @param string $type
     * @param Varien_Object $entity
     * @return string
     */
    public function getButton($type, Varien_Object $entity)
    {
        if (!$this->isMessagesAvailable($type, $entity)) {
            return '&nbsp;';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_helper')
            ->setId('giftmessage_button_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Retrive inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param boolean $dontDisplayContainer
     * @return string
     */
    public function getInline($type, Varien_Object $entity, $dontDisplayContainer=false)
    {
        if (!in_array($type, array('onepage_checkout'))
            && !$this->isMessagesAvailable($type, $entity)
        ) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('customer/giftmessage_inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Check availability of giftmessages for specified entity.
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param Mage_Core_Model_Store|integer $store
     * @return boolean
     */
    public function isMessagesAvailable($type, Varien_Object $entity, $store = null)
    {
        if ($type == 'items') {
            $items = $entity->getAllItems();
            if(!is_array($items) || empty($items)) {
                return Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
            }
            if($entity instanceof Mage_Sales_Model_Quote) {
                $_type = $entity->getIsMultiShipping() ? 'address_item' : 'item';
            }
            else {
                $_type = 'order_item';
            }

            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($this->isMessagesAvailable($_type, $item, $store)) {
                    return true;
                }
            }
        } elseif ($type == 'item') {
            return $this->_getDependenceFromStoreConfig(
                $entity->getPost()->getGiftMessageAvailable(),
                $store
            );
        } elseif ($type == 'order_item') {
            return $this->_getDependenceFromStoreConfig(
                $entity->getGiftMessageAvailable(),
                $store
            );
        } elseif ($type == 'address_item') {
            $storeId = is_numeric($store) ? $store : Mage::app()->getStore($store)->getId();

            if (!$this->isCached('address_item_' . $entity->getPostId())) {
                $this->setCached(
                    'address_item_' . $entity->getPostId(),
                    Mage::getModel('catalog/post')
                        ->setStoreId($storeId)
                        ->load($entity->getPostId())
                        ->getGiftMessageAvailable()
                );
            }
            return $this->_getDependenceFromStoreConfig(
                $this->getCached('address_item_' . $entity->getPostId()),
                $store
            );
        } else {
            return Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER, $store);
        }

        return false;
    }

    /**
     * Check availablity of gift messages from store config if flag eq 2.
     *
     * @param int $postGiftMessageAllow
     * @param Mage_Core_Model_Store|integer $store
     * @return boolean
     */
    protected function _getDependenceFromStoreConfig($postGiftMessageAllow, $store=null)
    {
        $result = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
        if ($postGiftMessageAllow === '' || is_null($postGiftMessageAllow)) {
            return $result;
        } else {
            return $postGiftMessageAllow;
        }
    }

    /**
     * Alias for isMessagesAvailable(...)
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getIsMessagesAvailable($type, Varien_Object $entity, $store=null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrive escaped and preformated gift message text for specified entity
     *
     * @param Varien_Object $entity
     * @return unknown
     */
    public function getEscapedGiftMessage(Varien_Object $entity)
    {
        $message = $this->getGiftMessageForEntity($entity);
        if ($message) {
            return nl2br($this->escapeHtml($message->getMessage()));
        }
        return null;
    }

    /**
     * Retrive gift message for entity. If message not exists return null
     *
     * @param Varien_Object $entity
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessageForEntity(Varien_Object $entity)
    {
        if($entity->getGiftMessageId() && !$entity->getGiftMessage()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            $entity->setGiftMessage($message);
        }
        return $entity->getGiftMessage();
    }

    /**
     * Retrive internal cached data with specified key.
     *
     * If cached data not found return null.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getCached($key)
    {
        if($this->isCached($key)) {
            return $this->_innerCache[$key];
        }

        return null;
    }

    /**
     * Check availability for internal cached data with specified key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key)
    {
        return isset($this->_innerCache[$key]);
    }

    /**
     * Set internal cache data with specified key
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_GiftMessage_Helper_Message
     */
    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    /**
     * Check availability for onepage checkout items
     *
     * @param array $items
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForQuoteItems($quote, $store=null)
    {
        foreach($quote->getAllItems() as $item) {
            if($this->isMessagesAvailable('item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check availability for multishiping checkout items
     *
     * @param array $items
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForAddressItems($items, $store=null)
    {
        foreach($items as $item) {
            if($this->isMessagesAvailable('address_item', $item, $store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrive gift message with specified id
     *
     * @param integer $messageId
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessage($messageId=null)
    {
        $message = Mage::getModel('customer/giftmessage');
        if(!is_null($messageId)) {
            $message->load($messageId);
        }

        return $message;
    }

}
