<?php
class Mage_Adminhtml_Block_Offline_Url_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('urlGrid');
        $this->setDefaultSort('url_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ins/url')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('url_id', array(
            'header'    => "ID",
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'url_id',
        ));
		
        
        $this->addColumn('url', array(
            'header'    => "URL",
            'align'     => 'center',
            'index'     => 'url'
        ));
        $this->addColumn('client_id', array(
            'header'    => "客户ID",
            'align'     => 'center',
            'index'     => 'client_id',
        ));
        $this->addColumn('status', array(
            'header'    => "状态",
            'align'     => 'center',
            'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
				
				'0' => '待处理',
				'1' => '处理中',
				'2' => '处理完成',
				'99' => '已同步',
				'-1' => '无效',
			),
        ));
        
		
        return parent::_prepareColumns();
    }

}
