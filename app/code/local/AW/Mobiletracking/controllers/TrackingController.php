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


class AW_Mobiletracking_TrackingController extends Mage_Core_Controller_Front_Action {
    
    /**
     *
     * @var obj
     */
    protected $_order = null;

    /**
     * @var array
     */
    protected $_secretKey = array(); 
    
    /**
     *  Behavior of _prepareNativeBlocks function differs depending on mode
    */
    public function viewAction($mode = AW_Mobiletracking_Helper_Data::GET_TRACKING) {
         
        $this->loadLayout();        
        $this->_prepareNativeBlocks($mode);
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('mobiletracking')->__('Order Tracking'));
        $this->renderLayout();
    }
    
    /**
     * 
     * View shipping status with the call of viewAction with different mode
     * @return type 
     * 
     */
    
    public function collectTracksAction() {
        $request = Mage::app()->getRequest()->getParams();

        if ($this->_inSecretMode()) {

            list($email, $orderNumber) = $this->_secretKey;
            $request[AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_EMAIL] = $email;
            $request[AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_NUMBER] = $orderNumber;
        }

        // Validate email address
        $orderEmail = @trim($request[AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_EMAIL]);
        $incrId = @trim(strip_tags($request[AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_NUMBER]));
        
        $request['email'] = $orderEmail;
        $request['number'] = $incrId;

        if (!Zend_Validate::is($orderEmail, 'EmailAddress')) {
            $this->_setRequestParams($request);
            Mage::getSingleton('core/session')->addError('Incorrect order credentials');
            $this->_redirectReferer();
            return;
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($incrId);
 
        if (!$order->getId() || strcasecmp($order->getCustomerEmail(),$orderEmail)) {
            $this->_setRequestParams($request);
            Mage::getSingleton('core/session')->addError('Incorrect order credentials');
            $this->_redirectReferer();
            return;
        }

        $this->_order = $order;
        
        $this->_unsRequestParams();

        $this->viewAction(AW_Mobiletracking_Helper_Data::VIEW_TRACKING);
    }
    
    private function _setRequestParams($request) {
       
       Mage::getSingleton('customer/session')->setData(AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_EMAIL,$request['email']);
       Mage::getSingleton('customer/session')->setData(AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_NUMBER,$request['number']);    
       
    }
    
    private function _unsRequestParams() {
        
       Mage::getSingleton('customer/session')->setData(AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_EMAIL,null);
       Mage::getSingleton('customer/session')->setData(AW_Mobiletracking_Helper_Data::MOBILETRACKING_ORDER_NUMBER,null); 
        
    }
    
    /**
     * 
     * Different behavior depending on the browser (mobile or desktop)
     * @param int $mode 
     * 
     */

    protected function _prepareNativeBlocks($mode) {

        $layout = $this->getLayout();
 
        if ($mode === AW_Mobiletracking_Helper_Data::VIEW_TRACKING) {

            $currentShipping = Mage::getModel('shipping/info')
                    ->setOrderId($this->_order->getId())
                    ->setProtectCode($this->_order->getProtectCode())
                    ->getTrackingInfoByOrder();

            $this->getAwMobileTracker()->setData('order', $this->_order);
            Mage::getBlockSingleton('mobiletracking/trackinginfo')->setTrackingInfo($currentShipping);
        }     
       
        if (Mage::helper('mobiletracking/track')->isMobile()) {
            
            $layout->getBlock('head')->removeItem('skin_css', 'css/styles.css');
            $layout->getBlock('head')->addItem('skin_css', 'aw_mobiletracking/css/styles.css');
            $layout->getBlock('root')->setAwMobileTracker($this->getAwMobileTracker())->setTemplate('aw_mobiletracking/1column.phtml')->setMode($mode);
            
        } else {
            
            $layout->getBlock('head')->addItem('skin_css', 'aw_mobiletracking/css/desktopStyles.css');
            $block = $layout->createBlock('mobiletracking/tracking')->setTemplate('aw_mobiletracking/1column.phtml')->setMode($mode);
            
            if ($mode === AW_Mobiletracking_Helper_Data::VIEW_TRACKING) {
                $block->setData('order', $this->_order);
            }

            $layout->getBlock('content')->insert($block);
        }         
      
    }
     
    protected function _inSecretMode() {

        $key = Mage::app()->getRequest()->getParam('key');
        if (!$key) {
            return false;
        }
        $key = strip_tags($key);
        if (!$key) {
            return false;
        }

        $data = Mage::getBlockSingleton('mobiletracking/order_email_tracker')->decodeHash($key);

        if (!$data) {
            return false;
        }

        $this->_secretKey = $data;

        return true;
    }

    public function getAwMobileTracker() {

        return Mage::getBlockSingleton('mobiletracking/tracking');
    }

}