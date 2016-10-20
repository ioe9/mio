<?php
class Mage_Adminhtml_Block_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $form->setFieldNameSuffix('account');

        $customer = Mage::registry('current_customer');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('customer')->__('账号信息')
        ));

		$fieldset->addField('email', 'text', array(
            'name'      => 'email',
            'label'     => '邮箱地址',
            'title'     => '邮箱地址',
            'required'  => false,
        ));
		
		$fieldset->addField('username', 'text', array(
            'name'      => 'username',
            'label'     => "用户名",
            'title'     => "用户名",
            'required'  => false,
        ));
        $fieldset->addField('gender', 'select', array(
            'name'      => 'gender',
            'label'     => "性别",
            'title'     => "性别",
            'required'  => false,
            'options'   => array('0'=>'保密','男','女'),
        ));
        $fieldset->addField('dob', 'date', array(
            'name'      => 'dob',
            'label'     => "生日",
            'title'     => "生日",
            'required'  => false,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'y-M-d',
        ));
        $fieldset->addField('phone', 'text', array(
            'name'      => 'phone',
            'label'     => '联系电话',
            'title'     => '联系电话',
            'required'  => false,
        ));
		
		$fieldset->addField('is_active', 'select', array(
            'name'      => 'is_active',
            'label'     => '状态',
            'title'     => '状态',
            'required'  => true,
            'options'   => array('1'=>'启用','0'=>'禁用'),
        ));
		
		
		
        if ($customer->getId()) {
            if (!$customer->isReadonly()) {
                // Add password management fieldset
                $newFieldset = $form->addFieldset(
                    'password_fieldset',
                    array('legend' => Mage::helper('customer')->__('Password Management'))
                );
                // New customer password
                $field = $newFieldset->addField('new_password', 'text',
                    array(
                        'label' => Mage::helper('customer')->__('New Password'),
                        'name'  => 'new_password',
                        'class' => 'validate-new-password'
                    )
                );
                $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass'));

                if (Mage::helper('customer')->getIsRequireAdminUserToChangeUserPassword()) {
                    $field = $newFieldset->addField('current_password', 'obscure',
                        array(
                            'name'  => 'current_password',
                            'label' => Mage::helper('customer')->__('Current Admin Password'),
                            'title' => Mage::helper('customer')->__('Current Admin Password'),
                            'required' => true
                        )
                    );
                    $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_adminpass'));
                }
            }
        } else {
            $newFieldset = $form->addFieldset(
                'password_fieldset',
                array('legend'=>"密码设置")
            );
            $field = $newFieldset->addField('password', 'text',
                array(
                    'label' => "密码",
                    'class' => 'input-text required-entry validate-password',
                    'name'  => 'password',
                    'required' => true
                )
            );
            $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass'));

            $fieldset->addField('sendemail', 'checkbox', array(
                'label' => "发送欢迎邮件",
                'name'  => 'sendemail',
                'id'    => 'sendemail',
            ));
            $customer->setData('sendemail', '1');
          
        }

        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }
}
