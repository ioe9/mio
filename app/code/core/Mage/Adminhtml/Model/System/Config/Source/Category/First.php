<?php
class Mage_Adminhtml_Model_System_Config_Source_Category_First
{
	protected $_categoriesOptions = null;
    public function toOptionArray($addEmpty = true)
    {
        $this->_categoriesOptions[''] = '-- 请选择 --';

		$collection = Mage::getModel('catalog/category')->getCollection(); 
		$arr = array();
		$levelArray1 = array();
		$levelArray2 = array();
		$levelArray3 = array();
		$levelArray4 = array();
		if ($collection->count()){ 
			foreach ($collection as $cat){ 
				
				
				if ($cat->getLevel()==0) {
					array_push($levelArray1,$cat);
				}
				if ($cat->getLevel()==1) {
					array_push($levelArray2,$cat);
				}
				if ($cat->getLevel()==2) {
					array_push($levelArray3,$cat);
				}
				if ($cat->getLevel()==3) {
					array_push($levelArray4,$cat);
				}
			} 
		}
		if (count($levelArray1)) {
			foreach ($levelArray1 as $_level1) {
				//$this->_categoriesOptions[$_level1->getId()] = $_level1->getName();
				if (count($levelArray2)) {
					foreach ($levelArray2 as $_level2) {
						if ($_level2->getParentId()==$_level1->getId()) {
							//$this->_categoriesOptions[$_level2->getId()] = '=='.$_level2->getName().'   ('.$_level2->getId().')';
							if (count($levelArray3)) {
								foreach ($levelArray3 as $_level3) {
									if ($_level3->getParentId()==$_level2->getId()) {
										$this->_categoriesOptions[$_level3->getId()] = ''.$_level3->getName().'   ('.$_level3->getId().')';
										if (count($levelArray4)){
											foreach ($levelArray4 as $_level4) {
												if ($_level4->getParentId()==$_level3->getId()) {
													$this->_categoriesOptions[$_level4->getId()] = '----'.$_level4->getName().'   ('.$_level4->getId().')';
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

        return $this->_categoriesOptions;
    }
}
