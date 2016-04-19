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

class AW_Sarp2_Helper_Humanizer extends Mage_Core_Helper_Data
{
    /**
     * Collect all information about subscription item
     * Result is array with the following keys:
     *
     * - website (Notices about available websites)
     *  - website (Imploded string with website names)
     *
     * - trial (Information about trial period)
     *  - period      (Notice with detailed cycles)
     *  - occurrences (Notice about repeats)
     *  - price       (Amount of trial period occurrence)
     *
     * - regular (Information about regular period)
     *  - period      (Notice with detailed cycles)
     *  - occurrences (Notice about repeats)
     *  - price       (Amount of regular period occurrence)
     *
     * - initial (Information about initial)
     *  - price (Amount of initial)
     *
     * @param AW_Sarp2_Model_Subscription_Item $item
     *
     * @return array
     */
    public function getAllInformation(AW_Sarp2_Model_Subscription_Item $item)
    {
        $result = array();
        if ($item) {
            $result['website'] = $this->getWebsiteInformation($item);
            $result['trial'] = $this->getTrialPeriodInformation($item);
            $result['regular'] = $this->getRegularPeriodInformation($item);
            $result['initial'] = $this->getInitialInformation($item);
        }
        return $result;
    }

    /**
     * Get information about available websites for current item
     * Result is array with imploded website names, contain the following key:
     *
     * - website (Imploded string with website names)
     *
     * @param AW_Sarp2_Model_Subscription_Item $item
     *
     * @return array
     */
    public function getWebsiteInformation(AW_Sarp2_Model_Subscription_Item $item)
    {
        $result = array();
        $type = $item->getTypeModel();
        $engine = $type->getEngineModel();
        $websites = Mage::helper('aw_sarp2/engine')->getWebsitesByEngine($engine);

        $websiteNames = array();
        foreach ($websites as $website) {
            $websiteNames[] = $website['name'];
        }
        $result['website'] = $this->__('Available on %s.', implode(', ', $websiteNames));
        return $result;
    }

    /**
     * Get information about trial period for current item
     * Result is array with the following key:
     *
     * - period      (Notice with detailed cycles)
     * - occurrences (Notice about repeats)
     * - price       (Amount of trial period occurrence)
     *
     * @param AW_Sarp2_Model_Subscription_Item $item
     *
     * @return array
     */
    public function getTrialPeriodInformation(AW_Sarp2_Model_Subscription_Item $item)
    {
        $result = array();
        $type = $item->getTypeModel();
        if ($type->getTrialIsEnabled()) {
            $result['period'] = $this->__('%s %s cycle.', $type->getPeriodLength(), $type->getPeriodUnit());
            $result['occurrences'] = $this->__('Repeat %s time.', $type->getTrialNumberOfOccurrences());
            $result['price'] = Mage::helper('core')->currency($item->getTrialPrice(), true, false);
        }
        return $result;
    }

    /**
     * Get information about regular period for current item
     * Result is array with the following key:
     *
     * - period      (Notice with detailed cycles)
     * - occurrences (Notice about repeats)
     * - price       (Amount of regular period occurrence)
     *
     * @param AW_Sarp2_Model_Subscription_Item $item
     *
     * @return array
     */
    public function getRegularPeriodInformation(AW_Sarp2_Model_Subscription_Item $item)
    {
        $result = array();
        $type = $item->getTypeModel();
        $result['period'] = $this->__('%s %s cycle.', $type->getPeriodLength(), $type->getPeriodUnit());
        if ($type->getPeriodIsInfinite()) {
            $result['occurrences'] = $this->__('Repeat until suspended or cancelled.');
        } else {
            $result['occurrences'] = $this->__('Repeat %s time.', $type->getPeriodNumberOfOccurrences());
        }
        $result['price'] = Mage::helper('core')->currency($item->getRegularPrice(), true, false);
        return $result;
    }

    /**
     * Get information about initial for current item
     * Result is array with the following key:
     *
     * - price (Amount of initial)
     *
     * @param AW_Sarp2_Model_Subscription_Item $item
     *
     * @return array
     */
    public function getInitialInformation(AW_Sarp2_Model_Subscription_Item $item)
    {
        $result = array();
        $type = $item->getTypeModel();
        if ($type->getInitialFeeIsEnabled()) {
            $result['price'] = Mage::helper('core')->currency($item->getInitialFeePrice(), true, false);
        }
        return $result;
    }
}