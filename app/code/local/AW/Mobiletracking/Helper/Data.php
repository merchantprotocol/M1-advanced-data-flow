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


class AW_Mobiletracking_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const GENERAL_TAB = 'general';
    
    const MODULE_NAME = 'mobiletracking';
    
    const PREFIX = 'aw';
    
    const MOBILETRACKING_ORDER_EMAIL = 'aw_mobiletracking_order_email';

    const MOBILETRACKING_ORDER_NUMBER = 'aw_mobiletracking_order_number';
    
    const GET_TRACKING = 0;
    
    const VIEW_TRACKING = 1;
    
    public function getStoreConfig($local, $path = self::GENERAL_TAB, $root = self::MODULE_NAME) {        
        
        return Mage::getStoreConfig("{$root}/{$path}/{$local}");        
        
    }
    
    
    public function getLogoUrl($local) {
         
        if(!$this->getStoreConfig('tracking_icon')) { return false; }

        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::PREFIX . "_". self::MODULE_NAME . "/". $this->getStoreConfig($local);
        
    }

     

}