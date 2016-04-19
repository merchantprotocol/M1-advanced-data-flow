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

class AW_Sarp2_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function getProfileUrl(Varien_Object $profile)
    {
        if ($profile instanceof AW_Sarp2_Model_Profile) {
            return $this->getUrl('aw_recurring/customer/view', array('id' => $profile->getId()));
        }
        return parent::getProfileUrl($profile);
    }

    /**
     * Prepare recurring payment profiles from the session
     */
    protected function _prepareLastRecurringProfiles()
    {
        $profileIds = Mage::getSingleton('checkout/session')->getLastRecurringProfileIds();
        if ($profileIds && is_array($profileIds)) {
            $collection = Mage::getModel('aw_sarp2/profile')->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $profileIds))
            ;
            if ($collection->getSize() !== count($profileIds)) {
                return parent::_prepareLastRecurringProfiles();
            }
            $profiles = array();
            foreach ($collection as $profile) {
                $profile->load($profile->getId());
                $profile->setData('schedule_description', $profile->getData('details/description'));
                $profiles[] = $profile;
            }
            if ($profiles) {
                $this->setRecurringProfiles($profiles);
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $this->setCanViewProfiles(true);
                }
            }
        }
        return $this;
    }
}