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


/**
 * Subscription subtotal total
 */
class AW_Sarp2_Model_Sales_Quote_Address_Total_Subscription_Subtotal
    extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    protected $_canAddAmountToAddress = false;
    protected $_itemRowTotalKey = 'row_total';

    private $_originalProductPrice = null;

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

    public function getLabel()
    {
        return Mage::helper('aw_sarp2')->__('Regular Payment');
    }

    /**
     * Address item initialization
     *
     * @param  $address
     * @param  $item
     *
     * @return bool
     */
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        } else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

        /**
         * Quote super mode flag mean what we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        } else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }

        if (!Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($item)) {
            return false;
        }

        if (null === $this->_originalProductPrice) {
            $this->_originalProductPrice = $product->getPrice();
        }

        $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')->getSubscriptionTypeOptionFromQuoteItem($item);
        $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item')->load($subscriptionTypeOption);

        //hack for compatibility with percent custom options
        $dataOfChanges = $this->_changePercentOptionsToFixed($product, $this->_originalProductPrice);
        $product->setPrice($subscriptionItem->getData('regular_price'));
        $finalPrice = $product->getFinalPrice($quoteItem->getQty());
        //rollback changes with percent custom options
        $this->_rollbackPercentOptions($dataOfChanges);

        $item->setPrice($finalPrice);
        $item->setBaseOriginalPrice($finalPrice);
        $item->calcRowTotal();

        $this->_addAmount($item->getRowTotal());
        $this->_addBaseAmount($item->getBaseRowTotal());
        $address->setTotalQty($address->getTotalQty() + $item->getQty());

        return true;
    }

    protected function _getAddressItems(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
    }

    /**
     * @param $product
     * @param $basePrice
     *
     * @return array
     */
    private function _changePercentOptionsToFixed($product, $basePrice)
    {
        $dataOfChanges = array();
        foreach ($product->getOptions() as $option) {
            $instanceList = array($option);
            if (null === $option->getPriceType()) {
                $itemOption = $product->getCustomOption('option_' . $option->getId());
                if (!is_object($itemOption)) {
                    continue;
                }
                $instanceList = array();
                foreach (explode(',', $itemOption->getValue()) as $optionId) {
                    $instanceList[] = $option->getValueById($optionId);
                }
            }
            foreach ($instanceList as $instance) {
                if (!is_object($instance) || $instance->getPriceType() != 'percent') {
                    continue;
                }
                $dataOfChanges[] = array(
                    'instance' => $instance,
                    'price' => $instance->getPrice()
                );
                $instance->setPriceType('fixed');
                $instance->setPrice(floatval($basePrice * $instance->getPrice() / 100));
            }
        }
        return $dataOfChanges;
    }

    /**
     * @param array $dataOfChanges
     * @return $this
     */
    private function _rollbackPercentOptions($dataOfChanges)
    {
        foreach ($dataOfChanges as $item) {
            $instance = $item['instance'];
            $instance->setPriceType('percent');
            $instance->setPrice($item['price']);
        }
        return $this;
    }
}
