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

class AW_Sarp2_Helper_Notification extends Mage_Core_Helper_Data
{
    public function getNotification()
    {
        $notifications = array();
        if (!$this->isSubscriptionEngineConfigured()) {
            $notifications[] = $this->__(
                '<strong>Subscription engine is not yet configured.</strong> '
                . 'Subscription engine is not yet configured. '
                . 'Visit <a href="%s">system configuration</a> to set this up.',
                Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit', array('section' => 'aw_sarp2'))
            );
        } elseif (!$this->isSubscriptionTypesExist()) {
            $notifications[] = $this->__(
                '<strong>Store has no subscription types configured yet.</strong> '
                . 'You have no subscription types configured. '
                . 'Visit <a href="%s">subscription types section</a> prior to creating subscriptions.',
                Mage::helper('adminhtml')->getUrl('aw_recurring_admin/adminhtml_subscription_type/index')
            );
        } elseif ($this->isSuspendedProfilesExist()) {
            $notifications[] = $this->__(
                '<strong>Store has suspended recurring profiles.</strong> '
                . 'You have suspended subscriptions profiles. '
                . 'Press <a href="%s">here</a> to view the list.',
                Mage::helper('adminhtml')->getUrl(
                    'aw_recurring_admin/adminhtml_profile/index',
                    array('status' => AW_Sarp2_Model_Source_Profile_Status::SUSPENDED)
                )
            );
        }
        return $notifications;
    }

    public function isSubscriptionEngineConfigured()
    {
        $result = false;
        foreach (Mage::app()->getWebsites() as $website) {
            $engineCode = Mage::app()->getWebsite($website['website_id'])
                ->getConfig('aw_sarp2/settings/subscription_engine')
            ;
            if ($engineCode) {
                $result = true;
            }
        }
        return $result;
    }

    public function isSuspendedProfilesExist()
    {
        return Mage::helper('aw_sarp2/profile')->isSuspendedProfileExist();
    }

    public function isSubscriptionTypesExist()
    {
        return Mage::getResourceModel('aw_sarp2/subscription_type_collection')->getSize() > 0;
    }
}