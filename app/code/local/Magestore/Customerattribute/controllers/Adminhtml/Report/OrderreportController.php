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
class Magestore_Customerattribute_Adminhtml_Report_OrderreportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
       $this->loadLayout()->_setActiveMenu('customer/customerattribute/report/orderreport');
       $this->renderLayout();
    }
    public function showreportAction()
    {   
        // Clear Old Season
        Mage::getSingleton('core/session')->setData('customerAttribute',Null);
        Mage::getSingleton('core/session')->setData('customerAddressAttribute',Null);
        Mage::getSingleton('core/session')->setData('order',Null);
        Mage::getSingleton('core/session')->setData('created_at',Null);
        Mage::getSingleton('core/session')->setData('attributeFilter',Null);
        Mage::getSingleton('core/session')->setData('attributeInput',Null);
        Mage::getSingleton('core/session')->setData('customerAttributeToOrder',Null);
        Mage::getSingleton('core/session')->setData('addressAttributeToOrder',Null);
        Mage::getSingleton('core/session')->setData('store_view',Null);
        Mage::getSingleton('core/session')->setData('created_at_order',Null);
        Mage::getSingleton('core/session')->setData('order_status',Null);
        Mage::getSingleton('core/session')->setData('empty_row',0);
        ///  -- End ---
        $customerAttributeToOrder = $this->getRequest()->getPost('customerattribute');
        $addressAttributeToOrder = $this->getRequest()->getPost('addressattribute');
        $empty_row = $this->getRequest()->getPost('empty_row');
        Mage::getSingleton('core/session')->setData('empty_row',$empty_row);
        if(!$customerAttributeToOrder && !$addressAttributeToOrder){
            Mage::getSingleton('core/session')->setData('messageToOrder',false);
            $this->_redirect('customerattributeadmin/adminhtml_report_orderreport/index');
        }else{
            Mage::getSingleton('core/session')->setData('messageToOrder',true);
        }    
        $storeId = $this->getRequest()->getPost('store_view');
        $created_at = $this->getRequest()->getPost('created_at');
        if(!empty($created_at['from']))
            $created_at['from']=date("Y-m-d",strtotime($created_at['from']));
        if(!empty($created_at['to']))
            $created_at['to']=date("Y-m-d",strtotime($created_at['to']));
        $order_status = $this->getRequest()->getPost('order_status');        
        Mage::getSingleton('core/session')->setData('customerAttributeToOrder',serialize($customerAttributeToOrder));
        Mage::getSingleton('core/session')->setData('addressAttributeToOrder',serialize($addressAttributeToOrder));
        Mage::getSingleton('core/session')->setData('store_view',$storeId);
        if(!empty($created_at['from'])|| !empty($created_at['to']) )
            Mage::getSingleton('core/session')->setData('created_at_order',serialize($created_at));
        else Mage::getSingleton('core/session')->setData('created_at_order',Null);
        if($order_status)
            Mage::getSingleton('core/session')->setData('order_status',serialize($order_status));
        else Mage::getSingleton('core/session')->setData('order_status',Null);
        $this->_redirect('customerattributeadmin/adminhtml_report_orderreport/index');
   }
   public function getAmountOrder()
    {
        return count(Mage::getResourceModel('sales/order_grid_collection'));
    }
   public function exportCsvAction()
    {   $block = new Magestore_Customerattribute_Block_Adminhtml_Report_Order_Orderreport;
        $attributeInputs=$block->getAttributeCodeToTable();
        $order_status = Mage::getSingleton('core/session')->getData('order_status');
        $data = $block->getOrderReport();
        $attributeFilters= $data['data'];
        $amountOrder=$this->getAmountOrder();
        $amountOrderFilter=$data['total_result'];
        $fileName   = 'Order_report_by_attributes.csv';   
        $content .= '"Total order result:'.$amountOrderFilter.'","Total order on store:'.$amountOrder.'",';
        $content.="\n";
        if(count($order_status)!= 0)
            $content.='"'.implode(',', $attributeInputs).'","Order Status","Number of Order","Relative percent (based on total result)","Absolute percent (based on total order)"';
        else
            $content.='"'.implode(',', $attributeInputs).'","Number of Order","Relative percent (based on total result)","Absolute percent (based on total order)"';
        $content.="\n";
        foreach($attributeFilters as $label => $attributeFilter)
        {
                if($amountOrder!=0 && !empty($attributeFilter))
                    $percentCustomerOnStore=100*$attributeFilter/$amountOrder;
                else $percentCustomerOnStore = 0;
                if($amountOrderFilter!=0 && !empty($attributeFilter))
                    $percentCustomerFilter=100*$attributeFilter/$amountOrderFilter;
                else $percentCustomerFilter=0;
                if(empty($attributeFilter)) $attributeFilter=0;
                $values= $label.',"'.$attributeFilter.'"'.',"'.$percentCustomerFilter.'%"'.',"'.$percentCustomerOnStore.'%"';
                $content.=$values;
                $content.="\n";
        }
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */    
    public function exportXmlAction()
    {   $block = new Magestore_Customerattribute_Block_Adminhtml_Report_Order_Orderreport;
        $attributeInputs=$block->getAttributeCodeToTable();
        $order_status = Mage::getSingleton('core/session')->getData('order_status');
        $data = $block->getOrderReport();
        $attributeFilters= $data['data'];
        $amountOrder=$this->getAmountOrder();
        $amountOrderFilter=$data['total_result'];
        $fileName   = 'Order_report_by_attributes.xml';
        $content    = '"Total order result:'.$amountOrderFilter.'","Total order on store:'.$amountOrder.'",""';
        if(count($order_status)!= 0){
            $content.="\n";
                $content.=implode(',', $attributeInputs).',"Order Status","Number of Order","Relative percent (based on total result)","Absolute percent (based on total order)"';
            $content.="\n";
        }else{
            $content.="\n";
                $content.=implode(',', $attributeInputs).',"Number of Order","Relative percent (based on total result)","Absolute percent (based on total order)"';
            $content.="\n";
        }
        foreach($attributeFilters as $label => $attributeFilter)
        {
                if($amountOrder!=0 && !empty($attributeFilter))
                    $percentCustomerOnStore=100*$attributeFilter/$amountOrder;
                else $percentCustomerOnStore = 0;
                if($amountOrderFilter!=0 && !empty($attributeFilter))
                    $percentCustomerFilter=100*$attributeFilter/$amountOrderFilter;
                else $percentCustomerFilter=0;
                if(empty($attributeFilter)) $attributeFilter=0;
                $values= $label.',"'.$attributeFilter.'"'.',"'.$percentCustomerFilter.'%"'.',"'.$percentCustomerOnStore.'%"';
                $content.=$values;
                $content.="\n";
        }
        $this->_prepareDownloadResponse($fileName, $content);
    }
}

