<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento community edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Mobiletracking
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Mobiletracking_Block_Tracking extends Mage_Core_Block_Template {
     
    public function getStoreConfig($info, $path = AW_Mobiletracking_Helper_Data::GENERAL_TAB, $root= AW_Mobiletracking_Helper_Data::MODULE_NAME) {
        
        return Mage::helper('mobiletracking')->getStoreConfig($info, $path, $root);      
        
    }
    
    public function getLogoUrl($path) {        
        
        return Mage::helper('mobiletracking')->getLogoUrl($path);  
        
    }
    
    public function getTrackingInfo() {        
        
        // return Mage::getBlockSingleton('mobiletracking/trackinginfo')->setTemplate('shipping/tracking/popup.phtml')->renderView();
        return Mage::getBlockSingleton('mobiletracking/trackinginfo')->setTemplate('aw_mobiletracking/shipping/popup.phtml')->renderView();
        
    }
    
    public function getAwMobileTracker() {
         
        return $this;
        
    }
    
}