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

class Magestore_Customerattribute_Adminhtml_Report_CustomerreportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
       $this->loadLayout()->_setActiveMenu('customer/customerattribute/report/customerreport');
       $this->renderLayout();
    }
    public function showReportAction()
    {
		Mage::getSingleton('core/session')->setData('customerAttribute',null);
		Mage::getSingleton('core/session')->setData('customerAddressAttribute',null);
		Mage::getSingleton('core/session')->setData('order',null);
		Mage::getSingleton('core/session')->setData('created_at',null);
		Mage::getSingleton('core/session')->setData('attributeFilter',null);
		Mage::getSingleton('core/session')->setData('show_empty_value',null);
		Mage::getSingleton('core/session')->setData('messageToCustomer',false);
		
		Mage::getSingleton('core/session')->setData('customerAttributeToOrder',Null);
        Mage::getSingleton('core/session')->setData('addressAttributeToOrder',Null);
        Mage::getSingleton('core/session')->setData('store_view',Null);
        Mage::getSingleton('core/session')->setData('created_at_order',Null);
        Mage::getSingleton('core/session')->setData('order_status',Null);
		Mage::getSingleton('core/session')->setData('messageToOrder',false);
		 ////////
		$f=$this->getRequest()->getPost();
		$customerAttribute=array();
		$customerAddressAttribute=array();
		$messageToCustomer=0;
		foreach($f as $label=>$value)
		{
			if($label!='form_key'&&$label!='store_view'&&$label!='created_at'&&$label!='order_value'&&$label!='show_empty_value')
			{
				$messageToCustomer++;
				$arr=explode(',',$label);
				if($arr[0]=='address')
				$customerAddressAttribute[$arr[1]]=$value;
				else
				$customerAttribute[$label]=$value;
			}
			else if($label=='order_value')
			{
			$order=$value;
			$messageToCustomer++;
			}
			else if($label=='created_at')
			{
			$createdAt=$value;
			// if($value['from']!=0||$value['to']!=0)
			// $messageToCustomer++;
			}
			else if($label=='show_empty_value')
			{
			$show_empty_value=$value;
			}
		}
			if(!empty($createdAt['from']))
			$createdAt['from']=date("Y-m-d",strtotime($createdAt['from']));
			if(!empty($createdAt['to']))
			$createdAt['to']=date("Y-m-d",strtotime($createdAt['to']));
		 Mage::getSingleton('core/session')->setData('customerAttribute',serialize($customerAttribute));
		 Mage::getSingleton('core/session')->setData('customerAddressAttribute',serialize($customerAddressAttribute));
		 Mage::getSingleton('core/session')->setData('order',serialize($order));
		 Mage::getSingleton('core/session')->setData('created_at',serialize($createdAt));
		 Mage::getSingleton('core/session')->setData('show_empty_value',serialize($show_empty_value));
		 if($messageToCustomer!=0)
		 Mage::getSingleton('core/session')->setData('messageToCustomer',true);
		 else
		 Mage::getSingleton('core/session')->setData('messageToCustomer',false);
			$this->_redirect('customerattributeadmin/adminhtml_report_customerreport/index');
           
    }
	/**
     * export grid item to CSV type
     */
	 public function getAmountCustomer()
	{
		$collection=Mage::getModel('customer/customer')->getCollection();
		return count($collection);
	}
    public function exportCsvAction()
    {//die('ee');
		$attributeInputs=Mage::getSingleton('core/session')->getData('attributeInput');
		$attributeInputs=unserialize($attributeInputs);
		$attributeFilters=Mage::getSingleton('core/session')->getData('attributeFilter');
		$attributeFilters=unserialize($attributeFilters);
		if($attributeFilters==null)
		{
			$customerAttribute=Mage::getSingleton('core/session')->getData('customerAttribute');
			$customerAttribute=unserialize($customerAttribute);
			$customerAddressAttribute=Mage::getSingleton('core/session')->getData('customerAddressAttribute');
			$customerAddressAttribute=unserialize($customerAddressAttribute);
			$order=Mage::getSingleton('core/session')->getData('order');
			$order=unserialize($order);
			$attributeFilters=Mage::helper('customerattribute/report')->isOrder($customerAttribute,$customerAddressAttribute,$order);
		}
		$amountCustomer=$this->getAmountCustomer();
		$amountCustomerFilter=$attributeFilters['amountCustomerFilter'];unset($attributeFilters['amountCustomerFilter']);
        $fileName   = 'Customerreport.csv';
        $content    = '"Total Results:'.$amountCustomerFilter.'","Total customers:'.$amountCustomer.'",""';
		$content.="\n";
			$content.=implode(',', $attributeInputs).',"Number of Customers","Relative percent (based on total result)","Absolute percent (based on total customers)"';
		$content.="\n";
		if($amountCustomerFilter==0)
		$amountCustomerFilter=1;
		foreach($attributeFilters as $label => $attributeFilter)
		{
			$percentCustomerOnStore=100*$attributeFilter/$amountCustomer;
			
			$percentCustomerFilter=100*$attributeFilter/$amountCustomerFilter;
			$values=explode('-',$label);
			$values=implode(',', $values).',"'.$attributeFilter.'"'.',"'.$percentCustomerFilter.'%"'.',"'.$percentCustomerOnStore.'%"';
			$content.=$values;
			$content.="\n";
		}
		$this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
		$attributeInputs=Mage::getSingleton('core/session')->getData('attributeInput');
		$attributeInputs=unserialize($attributeInputs);
		$attributeFilters=Mage::getSingleton('core/session')->getData('attributeFilter');
		$attributeFilters=unserialize($attributeFilters);
		if($attributeFilters==null)
		{
			$customerAttribute=Mage::getSingleton('core/session')->getData('customerAttribute');
			$customerAttribute=unserialize($customerAttribute);
			$customerAddressAttribute=Mage::getSingleton('core/session')->getData('customerAddressAttribute');
			$customerAddressAttribute=unserialize($customerAddressAttribute);
			$order=Mage::getSingleton('core/session')->getData('order');
			$order=unserialize($order);
			$attributeFilters=Mage::helper('customerattribute/report')->isOrder($customerAttribute,$customerAddressAttribute,$order);
		}
		$amountCustomer=$this->getAmountCustomer();
		$amountCustomerFilter=$attributeFilters['amountCustomerFilter'];unset($attributeFilters['amountCustomerFilter']);
        $fileName   = 'Customerreport.xml';
        $content    = '"Total Results:'.$amountCustomerFilter.'","Total customers:'.$amountCustomer.'",""';
		$content.="\n";
			$content.=implode(',', $attributeInputs).',"Number of Customers","Relative percent (based on total result)","Absolute percent (based on total customers)"';
		$content.="\n";
		if($amountCustomerFilter==0)
		$amountCustomerFilter=1;
		foreach($attributeFilters as $label => $attributeFilter)
		{
			$percentCustomerOnStore=100*$attributeFilter/$amountCustomer;
			$percentCustomerFilter=100*$attributeFilter/$amountCustomerFilter;
			$values=explode('-',$label); 
			$values=implode(',', $values).',"'.$attributeFilter.'"'.',"'.$percentCustomerFilter.'%"'.',"'.$percentCustomerOnStore.'%"';
			$content.=$values;
			$content.="\n";
		}
		$this->_prepareDownloadResponse($fileName, $content);
    }
}
