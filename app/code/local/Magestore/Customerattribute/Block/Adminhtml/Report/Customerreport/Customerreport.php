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
class Magestore_Customerattribute_Block_Adminhtml_Report_Customerreport_Customerreport extends Mage_Core_Block_Template
{
   public function _prepareLayout()
    {
		$this->setChild('report-graph',$this->getLayout()->createBlock('customerattribute/adminhtml_report_customerreport_chart'));
        return parent::_prepareLayout();
    }
    public function getCustomerAttribute()
    {   
		$attributeId=Mage::getStoreConfig('customerattribute/show_attribute_report_page/customer_attribute');
		$attributeId=explode(',',$attributeId);
		$customerAttribute=Mage::getModel('customer/attribute')->getCollection()->addFieldToFilter('main_table.attribute_id',$attributeId);
		return $customerAttribute;
    }
    public function getCustomerAddressAttribute()
    {
		$attributeId=Mage::getStoreConfig('customerattribute/show_attribute_report_page/customer_address_attribute');
		$attributeId=explode(',',$attributeId);
                $customerAddressAttribute = Mage::getResourceModel('customer/address_attribute_collection')->addFieldToFilter('main_table.attribute_id',$attributeId);
		
		return $customerAddressAttribute;
    }
    public function getEavAddressAttribute($attributeId)
    {       
        return Mage::getResourceModel('eav/entity_attribute_collection')
                        ->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
    }
}
?>
