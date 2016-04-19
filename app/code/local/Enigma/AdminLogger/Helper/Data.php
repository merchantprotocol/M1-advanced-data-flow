<?php
/**
* dasENIGMA.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://codecanyon.net/licenses/regular
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento community edition
* dasENIGMA does not guarantee correct work of this extension
* on any other Magento edition except Magento community edition.
* dasENIGMA does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   Enigma
* @package    Enigma_AdminLogger
* @version    1.0
* @copyright  Copyright (c) 2014 dasENIGMA. (http://codecanyon.net/user/dasEnigma/portfolio?ref=dasEnigma)
* @license    http://codecanyon.net/licenses/regular
*/
class Enigma_AdminLogger_Helper_Data extends Mage_Core_Helper_Abstract{
	public function addLog($actionType, $objectType, $objectId, $objectDescription, $description, $forceUser = false){
		if(mage::getStoreConfig('AdminLogger/general/enable') != 1){
			return;
		}
		if($forceUser){
			$userName = $forceUser;
		} else {
			$userName = $this->getCurrentUserName();
		}
		if($userName){
			mage::getModel('AdminLogger/Log')
				->setal_date(date('Y-m-d H:i:s'))
				->setal_user($userName)
				->setal_object_id($objectId)
				->setal_object_description($objectDescription)
				->setal_description($description)
				->setal_action_type($actionType)
				->setal_object_type($objectType)
				->save();
		}
	}
	
	public function getCurrentUserName(){
		$retour = null;
		if(Mage::getSingleton('admin/session')->getUser()){
			 $retour = Mage::getSingleton('admin/session')->getUser()->getusername();
		}
		return $retour;
	}
	
	public function getObjectType($object){
		$retour = '';
		$resourceName = $object->getResourceName();
		$resourceName = strtolower($resourceName);
		
		if($this->considerObjectType($resourceName)){		
			return strtolower($resourceName);
		} else {
			return null;
		}
	}
	
	public function getActionType($object){
		$retour = '';		
		if($object->getis_new()){
			$retour = Enigma_AdminLogger_Model_Log::kActionTypeInsert ;
		} else {
			$retour = Enigma_AdminLogger_Model_Log::kActionTypeUpdate ;
		}
		return $retour;
	}
	
	public function getObjectDescription($object){
		$retour = '';
		$objectType = $this->getObjectType($object);		
		switch ($objectType){
			case 'customer/customer':
				$retour = mage::helper('AdminLogger')->__('Customer %s (id %s)', $object->getName(), $object->getId());				
				break;
			case 'catalog/product':
				$retour = mage::helper('AdminLogger')->__('Product %s (id %s)', $object->getName(), $object->getId());				
				break;
			case 'catalog/category':
				$retour = mage::helper('AdminLogger')->__('Category %s (id %s)', $object->getName(), $object->getId());				
				break;				
			case 'tax/class':
				$retour = mage::helper('AdminLogger')->__('Tax class %s (id %s)', $object->getclass_name(), $object->getId());				
				break;
			case 'adminlogger/log':
				break;
			case 'customer/address':
				$retour = mage::helper('AdminLogger')->__('Address for customer %s (id %s)', $object->getCustomer()->getName(), $object->getId());				
				break;
			case 'cataloginventory/stock_item':
				$retour = mage::helper('AdminLogger')->__('Stock for product %s (id %s)', $object->getProductName(), $object->getId());								
				break;
			case 'customer/group':
				$retour = mage::helper('AdminLogger')->__('Customer group %s (id %s)', $object->getcustomer_group_code(), $object->getId());												
				break;
			case 'productreturn/rma':
				$retour = mage::helper('AdminLogger')->__('Product Return %s (id %s)', $object->getrma_ref(), $object->getId());												
				break;
			case 'checkout/agreement':
				$retour = mage::helper('AdminLogger')->__('Agreement %s (id %s)', $object->getName(), $object->getId());												
				break;
			case 'sales/order':
				$retour = mage::helper('AdminLogger')->__('Sales Order %s (id %s)', $object->getincrement_id(), $object->getId());												
				break;
			case 'catalogrule/rule':
				$retour = mage::helper('AdminLogger')->__('Catalog rule %s (id %s)', $object->getname(), $object->getId());												
				break;
			case 'salesrule/rule':
				$retour = mage::helper('AdminLogger')->__('Sales rule %s (id %s)', $object->getname(), $object->getId());												
				break;
			case 'purchase/manufacturer':
				$retour = mage::helper('AdminLogger')->__('Manufacturer %s (id %s)', $object->getman_name(), $object->getId());												
				break;
			case 'admin/user':
				$retour = mage::helper('AdminLogger')->__('User %s (id %s)', $object->getusername(), $object->getId());												
				break;				
			case 'cms/page':
				$retour = mage::helper('AdminLogger')->__('CMS Page %s (id %s)', $object->gettitle(), $object->getId());												
				break;				
			case 'cms/block':
				$retour = mage::helper('AdminLogger')->__('CMS Block %s (id %s)', $object->gettitle(), $object->getId());												
				break;				
			default :
				$retour = mage::helper('AdminLogger')->__($objectType.' (id %s)', $object->getId());												
				mage::log('Unable to find description for type '.$objectType);
				break;
		}		
		return $retour;
	}
	
	public function getActionDescription($object, $actionType){
		$retour = '';		
		switch ($actionType){
			case Enigma_AdminLogger_Model_Log::kActionTypeInsert :
				mage::log('Retrieve description for insert');
				break;
			case Enigma_AdminLogger_Model_Log::kActionTypeDelete :
				mage::log('Retrieve description for delete');				
				break;
			case Enigma_AdminLogger_Model_Log::kActionTypeUpdate :
				mage::log('Retrieve description for update');
				$data = $object->getData();
				$origData = $object->getOrigData();

				if($data && $origData){
					foreach($origData as $key => $value){
						if($this->considerField($key)){
							$newValue = '';
							if(isset($data[$key])){
								$newValue = $data[$key];
							}
							$oldValue = $value;
							$retour .= $this->compareForUpdate($key, $oldValue, $newValue);
						}
					}
				}
				else {
					if(!$data){
						mage::log('Data is null');
					}
					if(!$origData){
						mage::log('Orig Data is null');
					}
				}
				
				if($retour == ''){
					mage::log('Unable to find changes for '.$actionType.' for object '.$this->getObjectType($object));
				}
				break;
		}		
		return $retour;
	}
	
	private function compareForUpdate($key, $oldValue, $newValue){
		$retour = '';
		if(is_object($oldValue) && is_object($newValue)){		
			$oldData = $oldValue->getData();
			$newData = $newValue->getData();			
			foreach($newData as $key => $value){
				if(isset($oldData[$key])){
					$retour .= $this->compareForUpdate($key, $oldData[$key], $newData[$key]);
				} else {
					$retour .= $this->compareForUpdate($key, '', $newData[$key]);	
				}
			}
			return $retour;			
		}		
		$compared = false;
		if(is_array($oldValue) && is_array($newValue)){
			mage::log('#Compare Array');
			foreach($newValue as $key => $value){
				mage::log('Compare key '.$key);
				if(isset($oldValue[$key])){
					$retour .= $this->compareForUpdate($key, $oldValue[$key], $newValue[$key]);
				} else {
					$retour .= $this->compareForUpdate($key, '', $newValue[$key]);	
				}
			}
			return $retour;
		}		
		$logField = true;
		if(is_array($oldValue)){
			$logField = false;
		}
		if(is_array($newValue)){
			$logField = false;
		}
		if((($oldValue == '') && ($newValue == null)) || (($oldValue == null) && ($newValue == ''))){
			$logField = false;
		}
		if(is_numeric($oldValue) && is_numeric($newValue)){
			$oldValue = floatval($oldValue);
			$newValue = floatval($newValue);
		}
		if($oldValue == $newValue){
			$logField = false;
		}
		if($logField){
			$retour .= mage::helper('AdminLogger')->__('%s change from %s to %s', $key, $oldValue, $newValue).", ";	
		}
		return $retour;
	}
	
	public function considerField($fieldName){
	    if(!Mage::registry('adminlogger_fields_to_ignore')){
			mage::log('Load ignored fields in registry');
			$ignoredFields = mage::getStoreConfig('AdminLogger/advanced/fields_to_ignore');
			$t_ignoredFields = explode("\n", $ignoredFields);	
			for($i=0;$i<count($t_ignoredFields);$i++){
				$t_ignoredFields[$i] = trim($t_ignoredFields[$i]);
			}			
			Mage::register('adminlogger_fields_to_ignore', $t_ignoredFields);
        }
		if(in_array($fieldName, Mage::registry('adminlogger_fields_to_ignore'))){
			mage::log('Field '.$fieldName.' ignored ');
			return false;
		} else {
			mage::log('Field '.$fieldName.' considered ');
			return true;
		}
	}
	
	public function considerObjectType($objectType){
	    if(!Mage::registry('adminlogger_ignored_object_types')){
			mage::log('Load ignored object types in registry');
			$ignoredObjectTypes = mage::getStoreConfig('AdminLogger/advanced/object_to_ignore');
			$t_ignoredObjectTypes = explode("\n", $ignoredObjectTypes);
			for($i=0;$i<count($t_ignoredObjectTypes);$i++){
				$t_ignoredObjectTypes[$i] = trim($t_ignoredObjectTypes[$i]);
			}
			Mage::register('adminlogger_ignored_object_types', $t_ignoredObjectTypes);
        }
		if(in_array($objectType, Mage::registry('adminlogger_ignored_object_types'))){
			mage::log('Object type '.$objectType.' ignored ');
			return false;
		} else {
			mage::log('Object type '.$objectType.' considered ');
			return true;
		}		
	}
}
?>