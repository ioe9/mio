<?php
class Mage_Customer_Helper_Giftmessage_Url extends Mage_Core_Helper_Url
{
    /**
     * Retrive gift message save url
     *
     * @param Varien_Object $item
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(Varien_Object $item, $type, $params=array())
    {
         if($item->getGiftMessageId()) {
             $params = array_merge($params, array('message'=>$item->getGiftMessageId(), 'item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('customer/giftmessage/edit', $params);
         } else {
             $params = array_merge($params, array('item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('customer/giftmessage/new', $params);
         }
    }

    /**
     * Retrive gift message button block url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getButtonUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('customer/giftmessage/button', $params);
    }

    /**
     * Retrive gift message remove url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getRemoveUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('customer/giftmessage/remove', $params);
    }

    /**
     * Retrive gift message save url
     *
     * @param integer $itemId
     * @param string $type
     * @param string $giftMessageId
     * @param array $params
     * @return string
     */
    public function getSaveUrl($itemId, $type, $giftMessageId=null, $params=array())
    {
         if(!is_null($giftMessageId)) {
             $params = array_merge($params, array('message'=>$giftMessageId, 'item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('customer/giftmessage/save', $params);
         } else {
             $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('customer/giftmessage/save', $params);
         }
    }

}
