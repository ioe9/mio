<?php
class Mage_Adminhtml_Block_Catalog_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('postGrid');
        $this->setDefaultSort('post_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/post')->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('post_id', array(
            'header'    => "ID",
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'post_id',
        ));
		
        $this->addColumn('title', array(
            'header'    => "标题",
            'align'     => 'left',
            'index'     => 'title'
        ));
        $this->addColumn('post_user', array(
            'header'    => "作者",
            'align'     => 'center',
            'index'     => 'post_user',
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
        $this->addExportType('*/*/exportCsv',('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('adminhtml')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
       
        parent::_afterLoadCollection();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('post_id');
        $this->getMassactionBlock()->setFormFieldName('post');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('adminhtml')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('adminhtml')->__('Are you sure?')
        ));
        return $this;
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
