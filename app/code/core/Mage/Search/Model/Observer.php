<?php
/***
 * 事件监听文章信息变动
 */
class Mage_Search_Model_Observer
{
    /****
     * 删除文章事件
     */
    public function postDeleteAfter($observer) {
    	$post = $observer->getEvent()->getData('data_object');
    	Mage::getModel('search/search')->deleteDocument($post->getId());
    	return;
    }
    
    public function postSaveCommitAfter($observer) {
    	
    	$post = $observer->getEvent()->getData('data_object');
    	$status = $post->getData('status');
    	if (true) {
    		return;
    	}
    	if ($status) {
    		Mage::getModel('search/indexer')->updateIndex($post);
    	} else {
    		Mage::getModel('search/search')->deleteDocument($post->getId());
    	}
    	
    	//Mage::getModel('search/indexer')->rebuildIndex();
		return;
    }
}
?>
