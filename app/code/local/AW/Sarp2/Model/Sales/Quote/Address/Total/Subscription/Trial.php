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


class AW_Sarp2_Model_Sales_Quote_Address_Total_Subscription_Trial
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_itemRowTotalKey = 'subscription_trial_payment';

    protected $_canAddAmountToAddress = false;

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
        foreach ($items as $item) {
            if (Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($item)) {
                $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')->getSubscriptionTypeOptionFromQuoteItem($item);
                $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item')->load($subscriptionTypeOption);
                if (
                    Mage::helper('aw_sarp2')->isEnabled()
                    && $subscriptionItem->getTypeModel()
                    && $subscriptionItem->getTypeModel()->getTrialIsEnabled()
                    && $subscriptionItem->getTypeModel()->getEngineModel()
                    && $subscriptionItem->getTypeModel()->getEngineModel()->getPaymentRestrictionsModel()
                        ->isTrialSupported()
                ) {
                    $item->setData(
                        $this->_itemRowTotalKey,
                        (float)$address->getQuote()->getStore()->convertPrice($subscriptionItem->getData('trial_price'))
                    );
                    $item->setData('skip_compound_subscription_regular_payment', true);
                }
            }
        }
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

    public function getLabel()
    {
        return Mage::helper('aw_sarp2')->__('Trial Payment');
    }
}