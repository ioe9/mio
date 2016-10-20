<?php
class Mage_Adminhtml_Block_Catalog_Post_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
		$model = Mage::registry('current_post');
		$data = $model->getData();
		$data['id'] = $data['post_id'];
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
        
        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => '文章主图',
            'title'     => '文章主图',
            'required'  => false,
        ));
        $fieldset->addField('position', 'text', array(
            'name'      => 'position',
            'label'     => '排序',
            'title'     => '排序',
            'required'  => false,
        ));
        $fieldset->addField('url_out', 'text', array(
            'name'      => 'url_out',
            'label'     => '外部链接',
            'title'     => '外部链接',
            'required'  => false,
        ));
        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => '状态',
            'title'     => '状态',
            'required'  => false,
            'options'   => array('0'=>'禁用','1'=>'启用'),
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
