<?php
class Perception_Bannerpro_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isInStore($itemId) {
	  $currentStoreId = Mage::app()->getStore()->getId();
	  $item = Mage::getModel('bannerpro/bannerpro')->load($itemId);
	  $itemStores = $item->getStoreId();
	  $stores = explode(',', $itemStores);
	  if(in_array($currentStoreId, $stores) || in_array(0, $stores)) {
	   return true;
	  }
	  else {
	   return false;
	  }
	 }
}