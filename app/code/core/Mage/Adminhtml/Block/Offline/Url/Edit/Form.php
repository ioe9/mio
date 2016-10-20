<?php
class Mage_Adminhtml_Block_Offline_Url_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => '请输入URL,多个请换行输入',
            'class'    => 'fieldset-wide',
        ));
        $fieldset->addField('urls', 'textarea', array(
            'name'      => 'urls',
            'label'     => 'URL列表',
            'title'     => 'URL列表',
            'required'  => true,
            'style'     => 'width:100%;height:340px;',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
