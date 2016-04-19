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


class AW_Mobiletracking_Block_Widget_Block extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
    
    protected function _construct() {       
        
        $this->setTemplate('aw_mobiletracking/widget/tracker.phtml');
    
    }
    
    public function getTrackerModuleUrl() {
        
        return Mage::getUrl('awmobiletracking/tracking/view');
      
    }
    
    protected function _toHtml() { 
    
        if(Mage::app()->getRequest()->getModuleName() == AW_Mobiletracking_Helper_Data::PREFIX . AW_Mobiletracking_Helper_Data::MODULE_NAME) {
            
            return NULL;
            
        }
        
        return parent::_toHtml();
    
    }
  

}

