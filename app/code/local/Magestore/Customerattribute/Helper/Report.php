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
 * Customerattribute Helper
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Helper_Report extends Mage_Core_Helper_Abstract
{
	public function isOrder($customerAttribute,$customerAddressAttribute,$orders)
	{
		$show_empty_value=Mage::getSingleton('core/session')->getData('show_empty_value');
		$show_empty_value=unserialize($show_empty_value);
		$orderCollection=Mage::getResourceModel('sales/order_grid_collection');
		$arrs=$this->getAttribute($customerAttribute,$customerAddressAttribute);
		$optionCollections = Mage::getResourceModel('eav/entity_attribute_option_collection')
					  ->setStoreFilter(0);
		$optionCollection=array();
		 foreach($optionCollections as $a)
		 {
			$optionCollection[$a->getOptionId()]=$a->getData();
			
		 }
		 $countryLists = Mage::getModel('directory/country')->getResourceCollection()->loadByStore() ->toOptionArray(true);
		  $countryList=array();
		  foreach($countryLists as $county)
		  {
			$countryList[$county['value']]=$county['label'];
		  
		  }
		
		$customer_group = new Mage_Customer_Model_Group();
			$allGroups  = $customer_group->getCollection()->toOptionHash();
		foreach($arrs as $labelArrs=>$valueArrs)
		{
			unset($arrs[$labelArrs]);
			$labelArrs=$this->getOptions($labelArrs,$optionCollection,$countryList,$allGroups);
			$arrs[$labelArrs]=$valueArrs;
		}
		foreach($arrs as $labelArrs=>$valueArrs)
		{
			
			foreach($orders as $order)
			{	
				$labelTg=$labelArrs;
				unset($arrs[$labelArrs]);
				if(empty($labelTg))
				$labelTg=$order;
				else
				$labelTg.=','.$order;
				$arrs[$labelTg]=$this->getOrder($valueArrs,$order,$orderCollection);
			}
		}
		foreach($arrs as $arr=>$ar)
		{
			if(empty($arr))
			return null;
			else
			break;
		}
		$amountCustomerFilter=array();
		foreach($arrs as $labelArrs=>$valueArrs)
		{
			foreach($valueArrs as $labelValueArrs => $valueArr)
			if(empty($valueArr))
			unset($valueArrs[$labelValueArrs]);
			$amountCustomerFilter=array_merge($amountCustomerFilter,$valueArrs);
			$arrs[$labelArrs]=count($valueArrs);
			if($show_empty_value=='Donn’t show'&&$arrs[$labelArrs]==0)
			unset($arrs[$labelArrs]);
		}
			$CustomerId=array();
			foreach($amountCustomerFilter as $customerFilter)
			{
				$CustomerId[]=$customerFilter['entity_id'];
			}
			$arrs['amountCustomerFilter']=count(array_count_values($CustomerId));
				return $arrs;
	}
	public function getOrder($valueArrs,$order,$orderCollection)
	{
			foreach($valueArrs as $valueLabel=>$valueArr)
			{
				if(!($this->checkOrder($valueArr,$orderCollection)==1&&$order=='Ordered')&&!($this->checkOrder($valueArr,$orderCollection)==0&&$order=='Haven’t ordered'))
				unset($valueArrs[$valueLabel]);
			}
			return $valueArrs;
	}
	public function checkOrder($valueArr,$orderCollection)
	{
		foreach($orderCollection as $a)
		{
			if($a->getCustomerId()==$valueArr['entity_id'])
			return 1;
		}
		return 0;
	}
	public function getCustomerToCreatAt()
	{
		$createdAt=Mage::getSingleton('core/session')->getData('created_at');
		$createdAt=unserialize($createdAt);
		$collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
		$customer=array();
		foreach($collection as $col)
			{
			$time=date("Y-m-d",strtotime($col->getCreatedAt()));
			if($time>=$createdAt['from']&&$time<=$createdAt['to']&&!empty($createdAt['from'])&&!empty($createdAt['to']))
			{
				$customer[]=$col->getData();
			}
			else if(empty($createdAt['from'])==1&&$time<=$createdAt['to'])
			{
				$customer[]=$col->getData();
			}
			else if(empty($createdAt['to'])==1&&$time>=$createdAt['from'])
			{
				$customer[]=$col->getData();
			}
			else if(empty($createdAt['from'])==1&&empty($createdAt['to'])==1)
			{
			$customer[]=$col->getData();
			}
			}
			return $customer;
			
	}
	public function getAttribute($customerAttribute,$customerAddressAttribute)
	{       
		$arrs=array(' '=>$this->getCustomerToCreatAt());
		foreach($customerAttribute as $label=>$value)
		{
			foreach($arrs as $labelArrs=>$valueArrs)
			{
				foreach($value as $v)
				{	
					$labelTg=$labelArrs;
					unset($arrs[$labelArrs]);
					$labelTg.=','.$v;
					$arrs[$labelTg]=$this->getCustomer($valueArrs,$label,$v);
				}
				
			}
			
		}
		$arrs=$this->getAddressAttribute($arrs,$customerAddressAttribute);
		return $arrs;
	}
	public function getAddressAttribute($arrCustomerAttribute,$customerAddressAttribute)
	{
		$address = Mage::getModel('customer/address')->getCollection()->addAttributeToSelect('*');
		$customerAddress=array();
		 foreach($address as $add)
		 {
			$customerAddress[]=$add->getData();
		 }
		$label=null;
		$arrs=$arrCustomerAttribute;
		foreach($customerAddressAttribute as $label=>$value)
		{
			foreach($arrs as $labelArrs=>$valueArrs)
			{
				foreach($value as $v)
				{	
					$labelTg=$labelArrs;
					unset($arrs[$labelArrs]);
					$labelTg.=','.$v;
					$arrs[$labelTg]=$this->getCustomerAddress($valueArrs,$customerAddress,$label,$v);
				}		
			}
			
		}
		return $arrs;
	}
	public function getCustomerAddress($valueArrs,$customerAddress,$label,$v)
	{
		if($v=='false')
			{
				$v=0;
			}
			else if($v=='true')
			{
				$v=1;
            }
            foreach($valueArrs as $valueArrLabel=>$col1)
            {	
                    $i=0;
                    foreach ($customerAddress as $address)
                     {
                            if($address[$label]==$v&&$address['parent_id']==$col1['entity_id'])
                            {
                            $i++;
                            break;
                            }
                     }
                     if($i==0)
                     unset($valueArrs[$valueArrLabel]);
            }
            return $valueArrs;
	}

	public function getCustomer($valueArrs,$label,$v)
	{	
            $aa=explode('/',$v);
            if(count($aa)>1)
            {
            $v=$aa[1];                    
            }else if($v=='false')
			{
				$v=0;
			}
			else if($v=='true')
			{
				$v=1;
            }
			foreach($valueArrs as $valuelabel=>$col1)
            {
                    $option=explode(',',$col1[$label]);
                    if(!in_array($v,$option))
                  
                    unset($valueArrs[$valuelabel]);
            }
            return $valueArrs;	
	}
	public function getOptions($labelTg,$optionCollection,$countryList,$allGroups)
	{
		$labelTg=explode(',',$labelTg);
		foreach($labelTg as $label => $id)
			{	
			if($id==' ')
				unset($labelTg[$label]);
			else
				$labelTg[$label]=$this->getOptionlabel($id,$optionCollection,$countryList,$allGroups);
			}
			
			return implode(',',$labelTg);
				
	}
	public function getOptionlabel($id,$optionCollection,$countryList,$allGroups)
	{
		if($id=='false')
		{
			return 'No';
		}
		else if($id=='true')
		{
			return 'Yes';
		}
		else
		{
			$aa=explode('/',$id);
			if(count($aa)>1)
			{
				$id=$aa[1];
				return $allGroups[$id];
			}else 
			{
				if($optionCollection[$id]!=null)
				{
				return $optionCollection[$id]['value'];
				}
				else
				{
				return $countryList[$id];
				}
			}
		}
	}
	public function getOptionsGroup()
	{
		$customer_group = new Mage_Customer_Model_Group();
		$allGroups  = $customer_group->getCollection()->toOptionHash();
		$data=array();
		foreach($allGroups as $label => $allGroup)
		{
			$data['g/'.$label]=$allGroup;
		}
		return $data;
	}
	public function getOptionCountryLable($id)
	{
		$countryList = Mage::getModel('directory/country')->getResourceCollection()->addFieldToFilter('country_id',$id) ->loadByStore() ->toOptionArray(true);
		return $countryList[1]['label'];
	}
	public function getOptionCountry()
	{
		$countryList = Mage::getModel('directory/country')->getResourceCollection() ->loadByStore() ->toOptionArray(true);
		$data=array();
		unset($countryList[0]);
		foreach($countryList as $country)
		{
			$data[$country['value']]=$country['label'];
		}
		return $data;
	}	
}