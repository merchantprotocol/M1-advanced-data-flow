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


class AW_Sarp2_Model_Engine_Authorizenet_Payment_Authorizenet extends Widgetized_Level3_Model_Revolution
{
    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if (Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($quote)) {
            if ($checksBitMask & Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL) {
                $checksBitMask -= Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
            }
            if ($checksBitMask & Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX) {
                $total = 0;
                $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($quote);
                foreach ($items as $item) {
                    $total += $item->getBaseSubscriptionRowTotal();
                }
                $minTotal = $this->getConfigData('min_order_total');
                $maxTotal = $this->getConfigData('max_order_total');
                if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
                    return false;
                }
                $checksBitMask -= Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX;
            }
        }
        return parent::isApplicableToQuote($quote, $checksBitMask);
    }
}