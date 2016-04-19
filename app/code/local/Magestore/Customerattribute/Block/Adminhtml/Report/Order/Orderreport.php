<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Magestore_Customerattribute_Block_Adminhtml_Report_Order_Orderreport extends Mage_Core_Block_Template
{
   public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
   public function getCustomerAttribute()
    {   
       $attributeId=Mage::getStoreConfig('customerattribute/show_order_report_page/customer_attribute');
       $attributeId=explode(',',$attributeId);
       $customerAttributes=Mage::getModel('customer/attribute')->getCollection()->addFieldToFilter('main_table.attribute_id',$attributeId);
       return $customerAttributes;
    }
    public function getCustomerAddressAttribute()
    {
        $attributeId=Mage::getStoreConfig('customerattribute/show_order_report_page/customer_address_attribute');
        $attributeId=explode(',',$attributeId);
        $customerAddressAttributes = Mage::getResourceModel('customer/address_attribute_collection')->addFieldToFilter('main_table.attribute_id',$attributeId);
        return $customerAddressAttributes;
    }   
    public function getAttributeLabelToTable()
    { 
        $customerAttribute=Mage::getSingleton('core/session')->getData('customerAttributeToOrder');
        $customerAttribute=unserialize($customerAttribute);
        $addressAttribute=Mage::getSingleton('core/session')->getData('addressAttributeToOrder');
        $addressAttribute=unserialize($addressAttribute);
        $addressattributeType = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer_address');
        $data=array();
        $labels = $this->getAttributeLabel();
        foreach($customerAttribute as $attributeCode=>$value)
        {       
                
                $data[]= $labels[$attributeCode]; 
        }
        foreach($addressAttribute as $addressCode=>$value)
        {
                $data[]=$labels[$addressCode];
        }
        return $data;
    }
    public function getAttributeLabel()
    {   
        $labels = array();
        $customerattributes = $this->getCustomerAttribute();
        $addressattributes = $this->getCustomerAddressAttribute();
        foreach($customerattributes as $customerattribute){
            $labels[$customerattribute->getAttributeCode()] = $customerattribute->getFrontendLabel();
        }
        foreach ($addressattributes as $addressattribute){
            $labels[$addressattribute->getAttributeCode()] = $addressattribute->getFrontendLabel();
        }
        return $labels;
    }

    public function getAttributeFilter()
    {	
        $customerAttribute = Mage::getSingleton('core/session')->getData('customerAttributeToOrder');
        $customerAttribute = unserialize($customerAttribute);
        $addressAttribute = Mage::getSingleton('core/session')->getData('addressAttributeToOrder');
        $addressAttribute = unserialize($addressAttribute);
        $option_labels = Mage::helper('customerattribute')->getAllOptionLabel();
        $data=Mage::helper('customerattribute/report')->getAttribute($customerAttribute,$addressAttribute);
        $country = Mage::helper('customerattribute/report')->getOptionCountry();
        $group = Mage::helper('customerattribute/report')->getOptionsGroup();
        foreach ($data as $label=>$value){
            $new_label = substr($label,2);
            unset($data[$label]);
            $lbs = explode(',',$new_label);   
            $valueOption = array();
            foreach ($lbs as $lb){
                if($lb =='true'|| $lb=='false' ){
                    if($lb=='true') $valueOption[]='Yes';
                    else $valueOption[]='No';
                }elseif(!is_numeric($lb)){
                    if(!is_numeric(substr($lb, 2))){                        
                        $valueOption[] = $country[$lb];
                    }else{
                        
                        $valueOption[] = $group[$lb];
                    }
                }else $valueOption[] =$option_labels[$lb] ;
            }
            $new_label = implode(',', $valueOption);
            $data[$new_label] = $value;
            
        } 
        
        return $data;
    }
    public function getOrderReport()
    {        
        $storeId = Mage::getSingleton('core/session')->getData('store_view');
        $created_at = Mage::getSingleton('core/session')->getData('created_at_order');
        $order_status = Mage::getSingleton('core/session')->getData('order_status');
        $empty_row = Mage::getSingleton('core/session')->getData('empty_row');
        $order_status = unserialize($order_status);
        $created_at = unserialize($created_at);
        $orderstatus =   Mage::helper('customerattribute')->getAllOrderStatus(); 
        $data = $this->getAttributeFilter();
        $orderCollection =  Mage::helper('customerattribute')->getOrderCollection($storeId,$created_at);
        $number_order = 0;
        $total_result = array();
        foreach ($data as $label=>$value){
           if($order_status){
                foreach ($order_status as $status){
                    $new_label = $label;
                    unset($data[$label]);
                    $new_label = $new_label.','.$orderstatus[$status];                    
                    foreach ($value as $v){
                       $orders_data = Mage::helper('customerattribute')->getNumberOrder($v['entity_id'],$status,$orderCollection);
                       $number_order = $number_order + $orders_data['number'];
                       foreach ($orders_data['orderIds'] as $orderId){
                           if(!(int)in_array($orderId,$total_result))
                            $total_result[]=$orderId;
                       }
                    }
                    if(!$empty_row && $number_order==0)
                        unset($data[$new_label]);
                    else{
                        $data[$new_label] = $number_order;
                    } 
                    $number_order = 0;
                    
                }
            }else{
               foreach ($value as $v){
                    $orders_data = Mage::helper('customerattribute')->getNumberOrder($v['entity_id'],null,$orderCollection);
                    $number_order = $number_order + $orders_data['number'];
                    foreach ($orders_data['orderIds'] as $orderId){
                        if(!(int)in_array($orderId,$total_result))   
                        $total_result[]=$orderId;
                       }
                }
                if(!$empty_row && $number_order==0)
                    unset($data[$label]);
                else{
                    $data[$label] = $number_order;
                } 
                $number_order = 0;
            }
        }
        $result =  array();
        $result['data']=$data;
        $result['total_result'] = count($total_result);
        return $result;
    }
}
?>
