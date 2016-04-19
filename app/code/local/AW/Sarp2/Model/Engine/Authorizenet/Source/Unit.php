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


class AW_Sarp2_Model_Engine_Authorizenet_Source_Unit implements AW_Sarp2_Model_Source_SourceInterface
{
    const DAYS   = 'days';
    const MONTHS = 'months';
    const MONTHS_LABEL = "Months";
    const DAYS_LABEL   = "Days";

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        $preparedOptions = self::_getPreparedOptions();
        foreach (Mage::getModel('aw_sarp2/engine_authorizenet_restrictions')->getAvailableUnitOfTime() as $unit) {
            $optionArray[] = array(
                'value' => $unit,
                'label' => isset($preparedOptions[$unit]) ? $preparedOptions[$unit] : $unit,
            );
        }
        return $optionArray;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $preparedOptions = self::_getPreparedOptions();
        foreach (Mage::getModel('aw_sarp2/engine_authorizenet_restrictions')->getAvailableUnitOfTime() as $unit) {
            $array[$unit] = isset($preparedOptions[$unit]) ? $preparedOptions[$unit] : $unit;
        }
        return $array;
    }

    /**
     * @return array
     */
    protected function _getPreparedOptions()
    {
        $helper = Mage::helper('aw_sarp2');
        return array(
            self::DAYS   => $helper->__(self::DAYS_LABEL),
            self::MONTHS => $helper->__(self::MONTHS_LABEL),
        );
    }
}