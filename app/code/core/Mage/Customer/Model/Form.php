<?php
/***
 * 会员信息统一验证
 */
class Mage_Customer_Model_Form
{
	protected $_customer;
	protected $_isAjaxRequest;
	public function setIsAjaxRequest($flag) {
		$this->_isAjaxRequest = $flag;
	}
	/***
	 * TODO...
	 */
	public function validateData($data) {
		return true;
	}
	
    public function extractData($request)
    {
       
        
    }
	public function setCustomer($customer) {
		$this->_customer = $customer;
		return $this;
	}
}
