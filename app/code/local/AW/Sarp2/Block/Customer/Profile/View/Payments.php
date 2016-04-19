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

class AW_Sarp2_Block_Customer_Profile_View_Payments extends Mage_Core_Block_Template
{
    public function getInfoBoxTitle()
    {
        return $this->__('Subscription Payments');
    }

    public function getInfoBoxFields()
    {
        $profile = $this->getProfile();
        $item = $profile->getSubscriptionItem();
        $currency = Mage::getModel('directory/currency')->load($profile->getData('details/currency_code'));
        $fields = array(
            array(
                'title' => $this->__('Currency:'),
                'value' => $profile->getData('details/currency_code'),
            ),
            array(
                'title' => $this->__('Billing Amount:'),
                'value' => $currency->format($profile->getData('amount'), array(), false),
            ),
            array(
                'title' => $this->__('Shipping Amount:'),
                'value' => $currency->format($profile->getData('details/shipping_amount'), array(), false),
            ),
            array(
                'title' => $this->__('Tax Amount:'),
                'value' => $currency->format($profile->getData('details/tax_amount'), array(), false),
            ),
        );
        if ($item->getTypeModel()->getTrialIsEnabled()) {
            $fields[] = array(
                'title' => $this->__('Trial Amount:'),
                'value' => $currency->format($item->getTrialPrice(), array(), false),
            );
        }
        if ($item->getTypeModel()->getInitialFeeIsEnabled()) {
            $fields[] = array(
                'title' => $this->__('Initial Fee:'),
                'value' => $currency->format($item->getInitialFeePrice(), array(), false),
            );
        }
        return $fields;
    }

    /**
     * @return AW_Sarp2_Model_Profile
     */
    public function getProfile()
    {
        return Mage::registry('current_profile');
    }
}