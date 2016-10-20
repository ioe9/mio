<?php
class Mage_Search_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_maxQueryWords = 5;
	protected $_defaultLimit = 10;
	/***
	 * 通过搜索功能，根据传入参数组装参数
	 */
	public function query($params = array(), $needFilter = false, $q = false) {
		$result = array(
			'code' => 1, //表示查询成功返回
			'data' => array(),
			'total' => array(),
			'msg' => '',
		);
		$params['q'] = $this->escape($params['q']);		
		//特殊字符处理，空格进行
		if (!is_array($params) || !count($params)) {
			return false;
		}
		$pageIndex = 1;
		$pageSize = $this->_defaultLimit;
		
		//分页
		if (array_key_exists('pageIndex',$params) && $params['pageIndex']) {
			$pageIndex = (int)$params['pageIndex'];
		}
		if (array_key_exists('pageSize',$params) && $params['pageSize']) {
			$pageSize = (int)$params['pageSize'];
		}
		$queryText = $this->getQueryText($params, $needFilter , $q);
		$search = Mage::getSingleton('search/search')->loadQuery($queryText,$pageIndex,$pageSize);
        $count = $search->count();
       
        if($count) {
            $result['data'] = $search->getPosts();
        }
    	$result['total'] = $search->size();
        return $result;
	}
	public function getQueryText($params = array(), $needFilter = false, $q = false) {
		//$params = $this->escape($params);
		$params['q'] = $this->escape($params['q']);	
		//特殊字符处理，空格进行
		if (!is_array($params) || !count($params)) {
			return false;
		}
		
		$queryText = array();
		if (!$q) {
			if (array_key_exists('q',$params) && $params['q']) {
				$queryText[] = $this->getQueryStr($params['q']);
			} else {
				$queryText[] = 'q=*:*';
			}
		} else {
			//自定义关键字权重
			$queryText[] = $q;
		}
		return $queryText;
	}
	/***
	 * 根据关键字
	 */
	public function getQueryStr($q) {
		$str = false;
		$stringHelper = Mage::helper('core/string');
		$wordsLike = $stringHelper->splitWords($q, true, $this->_maxQueryWords);
		
		if (count($wordsLike)>1) {
			//标题->keyword->分类->功效->品牌
			$q = implode(' OR ', $wordsLike);
			$str = 'q=title:('.$q.')^1.5 OR keyword:('.$q.')^1.3 OR fulltext:('.$q.')';
		} else {
			//单个关键字的情况
			$str = 'q=title:('.$q.')^1.5 OR keyword:('.$q.')^1.3 OR fulltext:('.$q.')';
		}
		return $str;
	}
	public function escape($value)  
    {  
    	if (is_array($value)) {
    		foreach ($value as $_k => $_v) {
    			$value[$_k] = $this->escape($_v);
    		}
	    	return $value;
    	} else {
    		$value = str_replace(array('^',':','+','-','&'),'',$value);
    		//list taken from http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters  
	        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
			$replace = '\\\$1';
			return preg_replace($pattern, $pattern, $value);
    	}
	         
    }
}
