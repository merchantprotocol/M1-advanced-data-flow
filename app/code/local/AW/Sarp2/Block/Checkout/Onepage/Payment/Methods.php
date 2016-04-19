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
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

//compatibility with AW_Points
if (!@class_exists('AW_Sarp_Block_Checkout_Onepage_Payment_Methods_Parent')) {
    if (@class_exists('AW_Points_Block_Checkout_Onepage_Payment_Methods')
        && Mage::getConfig()->getModuleConfig('AW_Points')->is('active')
        && Mage::helper('points/config')->isPointsEnabled()
    ) {
        class AW_Sarp2_Block_Checkout_Onepage_Payment_Methods_Parent
            extends AW_Points_Block_Checkout_Onepage_Payment_Methods
        {
        }
    } else {
        class AW_Sarp2_Block_Checkout_Onepage_Payment_Methods_Parent 
            extends Mage_Checkout_Block_Onepage_Payment_Methods
        {
        }
    }
}

class AW_Sarp2_Block_Checkout_Onepage_Payment_Methods extends AW_Sarp2_Block_Checkout_Onepage_Payment_Methods_Parent
{
    //rewrite core method for displaying specific payment methods
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if (is_null($methods)) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = $this->helper('payment')->getStoreMethods($store, $quote);
            $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
            foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method)
                    && ($total != 0
                        || $method->getCode() == 'free'
                        || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles())
                        || Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($quote)
                    )
                ) {
                    $this->_assignMethod($method);
                } else {
                    unset($methods[$key]);
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }

    protected function _canUseMethod($method)
    {
        if (!Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($this->getQuote())) {
            return parent::_canUseMethod($method);
        }
        if (!$method || !$method->canUseCheckout()) {
            return false;
        }
        if (!$method->canUseForCountry($this->getQuote()->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($this->getQuote()->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = 0;
        $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($this->getQuote());
        foreach ($items as $item) {
            $total += $item->getBaseSubscriptionRowTotal();
        }
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
}