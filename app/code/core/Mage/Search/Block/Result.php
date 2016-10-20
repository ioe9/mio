<?php
class Mage_Search_Block_Result extends Mage_Core_Block_Template
{
	public function getPosts() {
		$helper = Mage::helper('search');
		$params = array();
		$request = Mage::app()->getRequest();
		$params['q'] = $request->getParam('q'); 
		$params['pageIndex'] = $request->getParam('page');
		$posts = $helper->query($params);
		return $posts;
	}
}
