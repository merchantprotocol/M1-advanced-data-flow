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


class AW_Sarp2_Model_Source_Subscription_Type_Visibility implements AW_Sarp2_Model_Source_SourceInterface
{
    const VISIBLE   = 1;
    const UNVISIBLE = 0;
    const VISIBLE_LABEL   = "Visible";
    const UNVISIBLE_LABEL = "Invisible";

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('aw_sarp2');
        return array(
            array(
                'value' => self::VISIBLE,
                'label' => $helper->__(self::VISIBLE_LABEL)
            ),
            array(
                'value' => self::UNVISIBLE,
                'label' => $helper->__(self::UNVISIBLE_LABEL)
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
            self::VISIBLE   => $helper->__(self::VISIBLE_LABEL),
            self::UNVISIBLE => $helper->__(self::UNVISIBLE_LABEL),
        );
    }
}