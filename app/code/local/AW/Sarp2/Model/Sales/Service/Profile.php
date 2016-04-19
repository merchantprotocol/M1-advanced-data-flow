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


class AW_Sarp2_Model_Sales_Service_Profile
{
    protected $_quote;
    protected $_recurringPaymentProfiles;

    public function __construct(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
    }

    public function getRecurringPaymentProfiles()
    {
        return $this->_recurringPaymentProfiles;
    }

    public function submitProfile()
    {
        /* @var $profile AW_Sarp2_Model_Profile */
        $profile = Mage::getModel('aw_sarp2/profile');
        Mage::helper('aw_sarp2/profile')->importQuoteToProfile($this->_quote, $profile);
        $profile->getSubscriptionEngineModel()->createRecurringProfile($profile);
        $profile->save();
        $profile->synchronizeWithEngine();
        $this->_recurringPaymentProfiles = array($profile);

        $this->_deleteSubscriptionItems();
        if (!$this->_quote->getAllVisibleItems()) {
            $this->_inactivateQuote();
            return;
        }
    }

    protected function _inactivateQuote()
    {
        $this->_quote->setIsActive(false);
        return $this;
    }

    protected function _deleteSubscriptionItems()
    {
        $subscriptionItems = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($this->_quote);
        foreach ($subscriptionItems as $item) {
            $item->isDeleted(true);
        }
        return $this;
    }
}