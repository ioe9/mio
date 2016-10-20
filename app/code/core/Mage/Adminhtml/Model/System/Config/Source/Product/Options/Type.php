<?php
class Mage_Adminhtml_Model_System_Config_Source_Post_Options_Type
{
    const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/post/options/custom/groups';

    public function toOptionArray()
    {
        $groups = array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --'))
        );

        $helper = Mage::helper('catalog');
		$customGroup = Mage::getModel('catalog/post_option')->getCustomGroups();
        foreach ($customGroup as $code=>$group) {
            $types = array();
            foreach ($group['types'] as $typeCode=>$type) {
               
                $types[] = array(
                    'label' => $type['label'],
                    'value' => $typeCode
                );
            }
            $groups[] = array(
                'label' => $group['label'],
                'value' => $types
            );
        }

        return $groups;
    }
}
