<?php
class Mage_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('customer_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
    	
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addFieldToSelect(array('email','created_at','group_id','username','phone'));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'    => "",
            'width'     => '50px',
            'index'     => 'customer_id',
            'type'  => 'number',
        ));
        $this->addColumn('username', array(
            'header'    => "用户名",
            'index'     => 'username'
        ));
        $this->addColumn('email', array(
            'header'    => "邮箱地址",
            'width'     => '150',
            'index'     => 'email'
        ));
		
        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  "组别",
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('phone', array(
            'header'    => "电话",
            'width'     => '100',
            'index'     => 'phone'
        ));

        $this->addColumn('customer_since', array(
            'header'    => "创建时间",
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        $this->addColumn('action',
            array(
                'header'    =>  "操作",
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => "编辑",
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv',('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customer')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customer')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('newsletter_subscribe', array(
             'label'    => Mage::helper('customer')->__('Subscribe to Newsletter'),
             'url'      => $this->getUrl('*/*/massSubscribe')
        ));

        $this->getMassactionBlock()->addItem('newsletter_unsubscribe', array(
             'label'    => Mage::helper('customer')->__('Unsubscribe from Newsletter'),
             'url'      => $this->getUrl('*/*/massUnsubscribe')
        ));

        $groups = $this->helper('customer')->getGroups()->toOptionArray();

        array_unshift($groups, array('label'=> '', 'value'=> ''));
        $this->getMassactionBlock()->addItem('assign_group', array(
             'label'        => Mage::helper('customer')->__('Assign a Customer Group'),
             'url'          => $this->getUrl('*/*/massAssignGroup'),
             'additional'   => array(
                'visibility'    => array(
                     'name'     => 'group',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => Mage::helper('customer')->__('Group'),
                     'values'   => $groups
                 )
            )
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
