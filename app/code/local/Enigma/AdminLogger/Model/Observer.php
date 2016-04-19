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
class Enigma_AdminLogger_Model_Observer extends Mage_Core_Model_Abstract{
	public function model_save_after(Varien_Event_Observer $observer){
		$object = $observer->getEvent()->getObject();
		$objectType = mage::helper('AdminLogger')->getObjectType($object);
		if($objectType){
			$objectId = $object->getId();
			$actionType = mage::helper('AdminLogger')->getActionType($object);
			$objectDescription = mage::helper('AdminLogger')->getObjectDescription($object);
			$actionDescription = mage::helper('AdminLogger')->getActionDescription($object, $actionType);			
			$log = true;
			if($actionType == Enigma_AdminLogger_Model_Log::kActionTypeUpdate && $actionDescription == ''){
				$log = false;
			}
			if($log){
				mage::helper('AdminLogger')->addLog($actionType,$objectType,$objectId,$objectDescription,$actionDescription);
			}
		}
	}
	
	public function model_save_before(Varien_Event_Observer $observer){
		$object = $observer->getEvent()->getObject(); 
		$objectType = mage::helper('AdminLogger')->getObjectType($object);		
		mage::log('Save before for '.$objectType);		
		if(!$object->getId()){
			$object->setis_new(true);
		}
		if(mage::getStoreConfig('AdminLogger/general/force_initial_data') == 1){
			$this->forceOrigDataLoad($object);
		}
	}
	
	public function model_delete_after(Varien_Event_Observer $observer){		
		$object = $observer->getEvent()->getObject(); 		
		$objectType = mage::helper('AdminLogger')->getObjectType($object);
		if($objectType){
			$objectId = $object->getId();
			$actionType = Enigma_AdminLogger_Model_Log::kActionTypeDelete ;
			$objectDescription = mage::helper('AdminLogger')->getObjectDescription($object);
			$actionDescription = '';			
			mage::helper('AdminLogger')->addLog($actionType,$objectType,$objectId,$objectDescription,$actionDescription);
		}
	}
	
	public function admin_user_authenticate_after(Varien_Event_Observer $observer){
		$object = $observer->getEvent(); 
		$user = $object->getuser();
		if($user->getId()){
			mage::helper('AdminLogger')->addLog(Enigma_AdminLogger_Model_Log::kActionTypeLogin,'user',$user->getId(),mage::helper('AdminLogger')->__('User ').$user->getusername(),mage::helper('AdminLogger')->__('Logged in at ').date('Y-m-d H:i'),'system');	
		}
	}
	
	public function admin_session_user_login_failed(Varien_Event_Observer $observer){
		$object = $observer->getEvent(); 
		$user = $object->getuser_name();
		mage::helper('AdminLogger')->addLog(Enigma_AdminLogger_Model_Log::kActionTypeLogin,'user',0,'User '.$user,'Login failed at '.date('Y-m-d H:i'),'system');				
	}
	
	public function catalog_product_website_update(Varien_Event_Observer $observer){
	}

	public function catalog_category_change_products(Varien_Event_Observer $observer){
		$object = $observer->getEvent(); 
		$category = $object->getcategory();
		$productIds = $object->getproduct_ids();
		mage::helper('AdminLogger')->addLog(Enigma_AdminLogger_Model_Log::kActionTypeUpdate,'Category',$category->getId(),'Category '.$category->getName(),'Products change to : '.join(',', $productIds));				
	}
	
	private function forceOrigDataLoad($object){
		$objectType = mage::helper('AdminLogger')->getObjectType($object);
		try{
		    if(!Mage::registry('adminlogger_force_orig_data')){
				mage::log('Load force orig data in registry');
				$forceOrigData = mage::getStoreConfig('AdminLogger/advanced/force_orig_data');
				$t_forceOrigData = explode("\n", $forceOrigData);		
				for($i=0;$i<count($t_forceOrigData);$i++){
					$t_forceOrigData[$i] = trim($t_forceOrigData[$i]);
				}
				Mage::register('adminlogger_force_orig_data', $t_forceOrigData);
	        }
	       	if(($objectType != '') && in_array($objectType, Mage::registry('adminlogger_force_orig_data'))){
				mage::log('Force Orig Data for '.$objectType);
				$newObject = mage::getModel($objectType)->load($object->getId());
				foreach($newObject->getData() as $key => $value){
					$object->setOrigData($key, $value);	
				}
			} else {
				mage::log('Do NOT force Orig Data for '.$objectType);
			}
		}
		catch (Exception $ex){
			mage::log('Erreur dans AdminLogger: '.$ex->getMessage().' - '.$ex->getTraceAsString());			
		}		
		return $object;	
	}
	
	public function handlerPruneLogs(){
		if(mage::getStoreConfig('AdminLogger/general/auto_prune') == 1){
			$pruneDelay = mage::getStoreConfig('AdminLogger/general/auto_prune_delay');
			Mage::getResourceModel('AdminLogger/Log')->Prune($pruneDelay);    	    	
		}
	}	
}