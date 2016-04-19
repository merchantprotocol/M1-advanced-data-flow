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
class Magestore_Customerattribute_Block_Adminhtml_Report_Customerreport_Chart extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('customerattribute/report/customer/chart.phtml');
    }
    public function _prepareLayout(){
        return parent::_prepareLayout();
    }
	public function getAmountCustomer()
	{
		$collection=Mage::getModel('customer/customer')->getCollection();
		return count($collection);
	}
	public function getAmountCustomerFilter($datas)
	{
		$total=0;
		foreach($datas as $data)
		{
			$total+=$data['value'];
		}
		return $total;
	}
	public function getAttributeInput()
	{
		$customerAttribute=Mage::getSingleton('core/session')->getData('customerAttribute');
		$customerAttribute=unserialize($customerAttribute);
		$customerAddressAttribute=Mage::getSingleton('core/session')->getData('customerAddressAttribute');
		$customerAddressAttribute=unserialize($customerAddressAttribute);
		$order=Mage::getSingleton('core/session')->getData('order');
		$order=unserialize($order);
		$data=array();
		$arrCustomer=$this->getCustomerAttributeLabel();
		$arrAddress=$this->getAddressAttributeLabel();
		foreach($customerAttribute as $label=>$value)
		{
			$data[]=$arrCustomer[$label];
		}
		foreach($customerAddressAttribute as $label=>$value)
		{
			$data[]=$arrAddress[$label];
		}
		if($order!=null)
		$data[]='Purcharse status';
		Mage::getSingleton('core/session')->setData('attributeInput',serialize($data));
		return $data;
	}
	public function getCustomerAttributeLabel()
	{
		$collections=Mage::getModel('customer/attribute')->getCollection();
		$data=array();
		foreach($collections as $collection)
		{
			$data[$collection->getAttributeCode()]=$collection->getFrontendLabel();
		}
		return $data;
	}
	public function getAddressAttributeLabel()
	{
		$entityTypeId = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer_address');
        
        $collections = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId); 
		$data=array();
		foreach($collections as $collection)
		{
			$data[$collection->getAttributeCode()]=$collection->getFrontendLabel();
		}
		return $data;
	}
	public function getAttributeFilter()
	{	
		$data=Mage::getSingleton('core/session')->getData('attributeFilter');
		$data=unserialize($data);
		if($data==null)
		{
		$customerAttribute=Mage::getSingleton('core/session')->getData('customerAttribute');
		$customerAttribute=unserialize($customerAttribute);
		$customerAddressAttribute=Mage::getSingleton('core/session')->getData('customerAddressAttribute');
		$customerAddressAttribute=unserialize($customerAddressAttribute);
		$order=Mage::getSingleton('core/session')->getData('order');
		$order=unserialize($order);
		$data=Mage::helper('customerattribute/report')->isOrder($customerAttribute,$customerAddressAttribute,$order);
		if(count($data<2000))
		Mage::getSingleton('core/session')->setData('attributeFilter',serialize($data));
		}
		return $data;
	}
	public function paging($data)
	{
		$subData=array_slice($data,0,10);
		return $subData;
	}
}

?>
