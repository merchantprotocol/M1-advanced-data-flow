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

class AW_Sarp2_Block_Customer_Profile_View_Schedule extends Mage_Core_Block_Template
{
    public function getInfoBoxTitle()
    {
        return $this->__('Profile Schedule');
    }

    public function getInfoBoxFields()
    {
        $profile = $this->getProfile();
        $item = $profile->getSubscriptionItem();
        $fields = array(
            array(
                'title' => $this->__('Start Date:'),
                'value' => $this->formatDate(
                    $profile->getStartDate(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false
                ),
            )
        );
        if ($item->getTypeModel()->getTrialIsEnabled()) {
            $trialValue = Mage::helper('aw_sarp2/humanizer')->getTrialPeriodInformation($item);
            $fields[] = array(
                'title' => $this->__('Trial Period:'),
                'value' => $trialValue['period'] . "\n" . $trialValue['occurrences'],
            );
        }
        $regularValue = Mage::helper('aw_sarp2/humanizer')->getRegularPeriodInformation($item);
        $fields[] = array(
            'title' => $this->__('Billing Period:'),
            'value' => $regularValue['period'] . "\n" . $regularValue['occurrences'],
        );
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