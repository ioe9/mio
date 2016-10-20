<?php
class Mage_Adminhtml_Block_Catalog_Event_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('eventGrid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/event')->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('event_id', array(
            'header'    => "ID",
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'event_id',
        ));
		
        $this->addColumn('title', array(
            'header'    => "标题",
            'align'     => 'left',
            'index'     => 'title'
        ));
        $this->addColumn('event_user', array(
            'header'    => "作者",
            'align'     => 'center',
            'index'     => 'event_user',
            'width'     => '100px',
        ));
		
        $this->addColumn('date_create', array(
            'header'    => "创建时间",
            'index'     => 'date_create',
            'width'     => '120px',
        ));
		$this->addColumn('updated_at', array(
            'header'    => "更新时间",
            'index'     => 'updated_at',
            'width'     => '120px',
        ));
        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
       
        parent::_afterLoadCollection();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
