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

class AW_Sarp2_Block_Customer_Profile_View extends Mage_Core_Block_Template
{
    /**
     * Retrieve current profile model instance
     *
     * @return AW_Sarp2_Model_Profile
     */
    public function getProfile()
    {
        return Mage::registry('current_profile');
    }

    public function getReferenceHtml()
    {
        return $this->getChildHtml('reference');
    }

    public function getPurchaseHtml()
    {
        return $this->getChildHtml('purchase');
    }

    public function getScheduleHtml()
    {
        return $this->getChildHtml('schedule');
    }

    public function getPaymentsHtml()
    {
        return $this->getChildHtml('payments');
    }

    public function getBillingHtml()
    {
        return $this->getChildHtml('billing');
    }

    public function getShippingHtml()
    {
        return $this->getChildHtml('shipping');
    }
}