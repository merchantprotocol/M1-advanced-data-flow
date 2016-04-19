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

class AW_Mobiletracking_Helper_Track extends Mage_Core_Helper_Abstract
{
    /**
     * iPhone Response
     */
    const IPHONE_RESPONSE = 'iPhone';

    /**
     * Android Response
     */
    const ANDROID_RESPONSE = 'Android';

    /**
     * BlackBerry Response
     */
    const BLACKBERRY_RESPONSE = 'BlackBerry';
    
    /**
     * iPhone Response
     */
    const WMOBILE_RESPONSE = 'IEMobile';

     
    protected $_target = null;     
    
    /**
     * Retrives is iPhone Flag
     * @return boolean
     */
    public function iPhone()
    {
        try {
            return (strpos($_SERVER['HTTP_USER_AGENT'], self::IPHONE_RESPONSE) !== false);
        } catch(Exception $e){
            return false;
        }
    }
    
    public function isMobile() {
        
       return $this->iPhone() || $this->Android() || $this->BlackBerry() || $this->winMobile();
  
    }

    /**
     * Retrives is Android Flag
     * @return boolean
     */
    public function Android()
    {
        try {
            return (strpos($_SERVER['HTTP_USER_AGENT'], self::ANDROID_RESPONSE) !== false);
        } catch(Exception $e){
            return false;
        }        
    }
    
    
     public function winMobile()
     {
           try {
              return (strpos($_SERVER['HTTP_USER_AGENT'], self::WMOBILE_RESPONSE) !== false);
        } catch(Exception $e){
             return false;
        }
     }

    /**
     * Find version in the http_user_agent
     *
     * @param string $pattern
     * @param string $text
     * @return string
     */
    protected function _findVersion($pattern, $text)
    {
        $regExp = "/({$pattern} (?:(\d+)\.)?(?:(\d+)\.)?(\*|\d+))/";
        $toDelete = "{$pattern} ";

        $matches = array();
        preg_match($regExp, $text, $matches);
        if (count($matches)){
            return str_replace($toDelete, "", $matches[0]);
        }
    }

    public function getAndroidVersion()
    {
        try {
            return $this->_findVersion( strtolower(self::ANDROID_RESPONSE), strtolower($_SERVER['HTTP_USER_AGENT']));
        } catch(Exception $e){
            # Do Nothing
        }        
    }
    
    /**
     * Retrives is BlackBerry Flag
     * @return boolean
     */
    public function BlackBerry()
    {
        try {
            return (strpos($_SERVER['HTTP_USER_AGENT'], self::BLACKBERRY_RESPONSE) !== false);
        } catch(Exception $e){
            return false;
        }
    }

    /**
     * Retrives customer session
     * @return Mage_Customer_Model_Session
     */
    protected function _customerSession()
    {
        
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrives Show Desktop flag value
     * @return boolean
     */
    public function getShowDesktop()
    {
        return $this->_customerSession()->getShowDesktop();
    }
 

    /**
     * Retrives Page Cache enabled flag
     * @return boolean
     */
    public function isPageCache()
    {                
        return Mage::app()->useCache('full_page');
    }        
     

    public function isOldAndroid()
    {
        return ($this->Android() && version_compare($this->getAndroidVersion(), '1.6', '<='));
    }

    
}
