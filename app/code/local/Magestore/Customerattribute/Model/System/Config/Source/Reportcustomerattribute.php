<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customerattribute Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Model_System_Config_Source_Reportcustomerattribute
{
	protected function checkCountOption($attribute){
		
		$options=Mage::helper('customerattribute')->getOptions($attribute->getAttributeId());		
		if((count($options)==0)&&($attribute->getFrontendInput()!='boolean')&&($attribute->getAttributeCode()!='group_id'))
			return false;
		return true;
	}
	public function toOptionArray($isMultiselect=false){
		

        $customerAttribute= Mage::getModel('customerattribute/customerattribute')->getCollection();		
		$customerAttribute->getSelect()
		->join(array('alias_table'=>'eav_attribute'),'main_table.attribute_id=alias_table.attribute_id',
				array('alias_table.frontend_input','alias_table.attribute_code','alias_table.frontend_label'),null);
		$customerAttribute->addFieldToFilter('frontend_input',array('in'=>array('select','multiselect','boolean')));
		//$customerAttribute->addOrder('attribute_code','ASC');
		$sort= array();
		
		foreach($customerAttribute as $attribute){
			if($this->checkCountOption($attribute))
				$sort[$attribute->getFrontendLabel()]=$attribute->getAttributeId();				
		}
				
		//Mage::helper('core/string')->ksortmultiByte($sort);
		
		$options=array();
		foreach($sort as $label=>$value){
			$options[]= array(
				'value'=> $value,
				'label'=>$label
			);
		}
		
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
	}
   
