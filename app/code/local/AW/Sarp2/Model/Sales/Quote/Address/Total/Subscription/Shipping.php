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


class AW_Sarp2_Model_Sales_Quote_Address_Total_Subscription_Shipping
    extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
    /**
     * Don't add/set amounts
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;
    protected $_canSetAddressAmount = false;

    protected $_itemRowTotalKey = 'shipping_amount';

    protected $_shouldGetAllItems = false;

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
        if (!count($items)) {
            return $this;
        }

        // estimate quote with all address items to get their row weights
        $this->_shouldGetAllItems = true;
        parent::collect($address);
        $address->setCollectShippingRates(true);
        $this->_shouldGetAllItems = false;
        // now $items contains row weight information

        // collect shipping rates for each item individually
        if ($address->getAddressType() != Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            return $this;
        }

        foreach ($items as $item) {
            if (!$item->getProduct()->isVirtual() && !$item->getFreeShipping()) {
                $address->setSarpFreeMethodWeight($item->getRowWeight());
                $address->requestShippingRates();
                if ($address->getShippingAmount()) {
                    $item->setBaseShippingAmount($address->getShippingAmount());
                    $item->setShippingAmount(
                        $address->getQuote()->getStore()->convertPrice($item->getBaseShippingAmount(), false)
                    );
                }
            }
        }
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

    protected function _getAddressItems(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->_shouldGetAllItems) {
            return $address->getAllItems();
        }
        return Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
    }
}