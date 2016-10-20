<?php
class Mage_Search_Model_Indexer
{
	protected $_maxPostSize = 2000;
	
	/****
	 * 删除指定postIds
	 */
	public function cleanIndex($postIds)
    {
    	if (!$postIds) {
    		return;
    	}
        try {
            $search = Mage::getModel('search/search');
            if(is_numeric($postIds)) {
                $search->deleteDocument($postIds);
            } else if(is_array($postIds)) {
                $search->deleteDocuments($postIds);
            }
            $search->commit();
            $search->optimize();
            
        } catch (Exception $e) {
           
            return;
        }
        return;
    }
	
    /**
	* 只有全部重建索引才会走这里
    */
    public function rebuildIndex()
    {
    	$maxSize = $this->_maxPostSize;
		$curPage = 1;
		while ($curPage) {
			$postCollection = Mage::getResourceModel('catalog/post_collection')
	            ->addFieldToFilter('status',1)
	            ->setCurPage($curPage)
	     		->setPageSize($maxSize);
	    	$count = count($postCollection);

	        if ($count<$maxSize) {
	        	$curPage = 0;
	        } else {
	        	$curPage++;
	        }
	        if ($count) {
		        $documents = array();
		        foreach ($postCollection as $post) {
		            
		            $data = $this->formatPostData($post);
		            $document = Mage::getModel('search/document');
		            
					foreach ($data as $k => $v) {
						if (is_array($v)) {
							foreach ($v as $_v) {
								$document->addField($k,$_v);
							}
						} else {
							$document->addField($k,$v);
						}
					}
					$documents[] = $document;
	    			
		        }
		        
		        if (count($documents)) {
		        	try {
			            $search = Mage::getModel('search/search');
		            	$search->addDocuments($documents); 
			            $search->commit();
			            $search->optimize();
			           
			            Mage::log('IndexerAll Success',false,'solr.log');
			        } catch (Exception $e) {
			            Mage::log('IndexerAll Error:'.$e->getMessage(),false,'solr.log');
			        }
		        }	
	        }
		}
        return true;
    }
    
    /***
     * 格式化产品数据
     */
    public function formatPostData($post) {
    	$data = array();
		//排序字段
		/**************** 新增字段推送 end ******************/
		$data['id'] = $post->getId();
		$data['post_id'] = $post->getId();
		$data['title'] = $post->getData('title');
        $data['sdesc'] = $post->getData('sdesc');
        $data['desc'] = $post->getData('desc');
        $data['keyword'] = $post->getData('meta_keywords');
        $data['username'] = Mage::getModel('admin/user')->load($post->getData('post_user'))->getUsername();
        $data['date'] = $post->getData('publish_at');
        return $data;
    }
    
    public function updateIndex($post) {
    	$data = $this->formatPostData($post);
    	$documents = array();
    	$search = Mage::getModel('search/search');
        $document = Mage::getModel('search/document');
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $_v) {
					$document->addField($k,$_v);
				}
			} else {
				$document->addField($k,$v);
			}
		}
		$documents[] = $document;
    	$search->addDocuments($documents); 
        $search->commit();
    }
}