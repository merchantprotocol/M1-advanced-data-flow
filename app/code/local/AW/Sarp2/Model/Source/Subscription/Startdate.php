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


class AW_Sarp2_Model_Source_Subscription_Startdate implements AW_Sarp2_Model_Source_SourceInterface
{
    const DEFINED_BY_CUSTOMER_CODE       = 1;
    const MOMENT_OF_PURCHASE_CODE        = 2;
    const LAST_DAY_OF_CURRENT_MONTH_CODE = 3;
    const EXACT_DAY_OF_MONTH_CODE        = 4;
    const DEFINED_BY_CUSTOMER_LABEL       = "Defined by customer";
    const MOMENT_OF_PURCHASE_LABEL        = "Moment of purchase";
    const LAST_DAY_OF_CURRENT_MONTH_LABEL = "Last day of current month";
    const EXACT_DAY_OF_MONTH_LABEL        = "Exact day of month";

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('aw_sarp2');
        return array(
            array(
                'value' => self::DEFINED_BY_CUSTOMER_CODE,
                'label' => $helper->__(self::DEFINED_BY_CUSTOMER_LABEL)
            ),
            array(
                'value' => self::MOMENT_OF_PURCHASE_CODE,
                'label' => $helper->__(self::MOMENT_OF_PURCHASE_LABEL)
            ),
            array(
                'value' => self::LAST_DAY_OF_CURRENT_MONTH_CODE,
                'label' => $helper->__(self::LAST_DAY_OF_CURRENT_MONTH_LABEL)
            ),
            array(
                'value' => self::EXACT_DAY_OF_MONTH_CODE,
                'label' => $helper->__(self::EXACT_DAY_OF_MONTH_LABEL)
            ),
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('aw_sarp2');
        return array(
            self::DEFINED_BY_CUSTOMER_CODE       => $helper->__(self::DEFINED_BY_CUSTOMER_LABEL),
            self::MOMENT_OF_PURCHASE_CODE        => $helper->__(self::MOMENT_OF_PURCHASE_LABEL),
            self::LAST_DAY_OF_CURRENT_MONTH_CODE => $helper->__(self::LAST_DAY_OF_CURRENT_MONTH_LABEL),
            self::EXACT_DAY_OF_MONTH_CODE        => $helper->__(self::EXACT_DAY_OF_MONTH_LABEL),
        );
    }

    /**
     * @param string|integer
     *
     * @return string|null
     */
    public static function getSubscriptionStartDateLabelByCode($code)
    {
        $code = (int)$code;
        $source = self::toOptionArray();
        foreach ($source as $item) {
            if ($item['value'] === $code) {
                return $item['label'];
            }
        }
        return null;
    }
}