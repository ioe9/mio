<?php
class Mage_Search_Model_Search extends Apache_Solr_Service
{
    
    /**
     * Represents a Solr response.
     *
     * @var Apache_Solr_Response
     */
    protected $_response;
    
    /**
     * Constructor, retrieve config for connection to solr server.
     */
    public function __construct()
    {
        $_host = Mage::getStoreConfig('solr/server/host');
        $_port = Mage::getStoreConfig('solr/server/port');
        $_path = Mage::getStoreConfig('solr/server/path');
        
        parent::__construct($_host,$_port,$_path);
    }
    
    public function useSlave() {
    	$_host = Mage::getStoreConfig('solr/server_slave/host');
        $_port = Mage::getStoreConfig('solr/server_slave/port');
        $_path = Mage::getStoreConfig('solr/server_slave/path');
        if ($_host && $_port && $_path) {
        	$this->setHost($_host);
	    	$this->setPort($_port);
	    	$this->setPath($_path);
        }	
    	return $this;
    }
    
    /**
     * 查询统一用Slave
     */
    public function loadQuery($params=array(),$curPage=1,$pageSize=10)
    {
    	$this->useSlave();
        if(!$this->_response) {
            $response = $this->search($params,($curPage-1)*$pageSize,$pageSize,'POST');
            $this->setResponse($response->response);
            
        }
        
        return $this;
    }
    
    /**
     * Delete All documents
     * 
     * @return Mage_Search_Model_Search
     */
    public function deleteAllDocuments()
    {
        $this->deleteByQuery('*:*');
        return $this;
    }
    
    /**
     * Delete specific document
     * 
     * @param int $postId
     * @return Mage_Search_Model_Search
     */
    public function deleteDocument($postId)
    {
        $this->deleteByQuery('post_id:'.$postId);
        
        return $this;
    }
    
    /**
     * Delete specifics document
     * 
     * @param array $postId
     * @return Mage_Search_Model_Search
     */
    public function deleteDocuments($postIds)
    {
        foreach($postIds as $id) {
            $this->deleteByQuery('post_id:'.$id);
        }
        
        return $this;
    }
    
    /**
     * Set Solr response
     * 
     * @param $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }
    /**
     * Extract post ids and score in Solr response
     * 
     * @return array $ids
     */
    public function getPosts()
    {
        $posts = array();
        foreach($this->_response->docs as $doc) {
            $tmp = array(
            	'post_id' => $doc->post_id,
            	'title' => $doc->title,
            	'sdesc' => $doc->sdesc,
            	'username' => $doc->username,
            	'date' => $doc->date,
            );
            $posts[] = $tmp;
        }
        return $posts;
    }

    
    /**
     * Retreive documents count
     * 
     * @return int
     */
    public function count()
    {
        return count($this->_response->docs);
    }
    
    public function size() {
    	return $this->_response->numFound;
    }
    
}