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


class AW_Sarp2_Block_Checkout_Total_Subscription extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'aw_sarp2/checkout/total/subscription.phtml';

    public function getTotalItemDetails(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getSubscriptionTotalDetails();
    }

    public function getItemDetailsRowIsCompounded(Varien_Object $row)
    {
        return $row->getIsCompounded();
    }

    public function getItemDetailsRowLabel(Varien_Object $row)
    {
        return $row->getLabel();
    }

    public function getItemDetailsRowAmount(Varien_Object $row)
    {
        return $row->getAmount();
    }

    public function getItemName(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getName();
    }

    public function getItemRowTotal(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getSubscriptionRowTotal();
    }

    public function formatPrice($amount)
    {
        return $this->_store->formatPrice($amount, false);
    }

    protected function _toHtml()
    {
        $total = $this->getTotal();
        $items = $total->getItems();
        if ($items) {
            foreach ($total->getData() as $key => $value) {
                $this->setData("total_{$key}", $value);
            }
            return parent::_toHtml();
        }
        return '';
    }
}