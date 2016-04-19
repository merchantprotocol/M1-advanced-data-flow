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


class AW_Sarp2_Model_Engine_Paypal_Payment_Express extends Mage_Paypal_Model_Express
{
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('aw_recurring/express/start');
    }

    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if (
            $checksBitMask & Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
            && Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($quote)
        ) {
            $checksBitMask -= Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
        }
        return parent::isApplicableToQuote($quote, $checksBitMask);
    }
}