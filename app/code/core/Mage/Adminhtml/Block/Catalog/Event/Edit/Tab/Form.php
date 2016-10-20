<?php
class Mage_Adminhtml_Block_Catalog_Event_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Define Form settings
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        Mage::app()->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
		$model = Mage::registry('current_event');
		$data = $model->getData();
		$data['id'] = $data['event_id'];
        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => '基本信息设置',
            'class'    => 'fieldset-wide',
        ));
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
            ));
        }
        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => '标题',
            'title'     => '标题',
            'required'  => true,
        ));
        $fieldset->addField('type', 'select', array(
            'name'      => 'type',
            'label'     => '类型',
            'title'     => '类型',
            'required'  => false,
            'options'   => Mage::getSingleton('catalog/event')->getTypeOptions(),
        ));
        $fieldset->addField('region', 'select', array(
            'name'      => 'region',
            'label'     => '所在地区',
            'title'     => '所在地区',
            'required'  => false,
            'options'   => Mage::getSingleton('catalog/event')->getRegionOptions(),
        ));
        $fieldset->addField('address', 'text', array(
            'name'      => 'address',
            'label'     => '详细地址',
            'title'     => '详细地址',
            'required'  => false,
        ));
        $dateFormatIso = 'M/d/yyyy';
        $fromDateFiled = $fieldset->addField('date_from', 'date', array(
            'name'   => 'date_from',
            'label'  => "起始时间",
            'title'  => "起始时间",
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        
        $fromDateFiled = $fieldset->addField('date_to', 'date', array(
            'name'   => 'date_to',
            'label'  => "截止时间",
            'title'  => "截止时间",
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => '活动主图',
            'title'     => '活动主图',
            'required'  => false,
        ));
		
		$fieldset->addField('url_type', 'text', array(
            'name'      => 'url_type',
            'label'     => '链接类型',
            'title'     => '链接类型',
            'required'  => false,
        ));
        $fieldset->addField('url_out', 'text', array(
            'name'      => 'url_out',
            'label'     => '外部链接URL',
            'title'     => '外部链接URL',
            'required'  => false,
        ));
        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => 'URL Key',
            'title'     => 'URL Key',
            'required'  => false,
        ));
        
        $fieldset->addField('desc', 'editor', array(
            'name'      => 'desc',
            'label'     => '详细',
            'title'     => '详细',
            'required'  => false,
            'style' => 'height:400px',
            
        ));
        $fieldset->addField('sdesc', 'textarea', array(
            'name'      => 'sdesc',
            'label'     => '简介',
            'title'     => '简介',
            'required'  => false,
            'style' => 'height:80px',
            
        ));
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
