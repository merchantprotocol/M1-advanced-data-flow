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


class AW_Mobiletracking_Block_Order_Email_Tracker extends Mage_Core_Block_Template {

    public function getTrackerUrl() {

        $order = $this->getOrder();

        return Mage::getUrl('awmobiletracking/tracking/collectTracks', array('_store' => $order->getStoreId(), 'key' => $this->_getHash()));
    }

    /**
     *  get secret key compund of 
     *  order incrementId and billing email address
     *  like encfrementId||emailaddress
     */
    protected function _getHash() {

        $order = $this->getOrder();

        $email = $order->getCustomerEmail();
        $incrementId = $order->getIncrementId();

        $key = Mage::helper('core')->encrypt("{$email}||{$incrementId}");
        $key = Mage::helper('core/url')->getEncodedUrl($key);

        return $key;
    }

    /**
     * @param string $hash
     * @return bool + array 
     */
    public function decodeHash($hash) {

        $key = Mage::helper('core/url')->urlDecode($hash);
         
        $data = Mage::helper('core')->decrypt($key);

        $data = explode('||', $data);

        if (count($data) != 2) {
            return false;
        }

        return $data;
    }

}