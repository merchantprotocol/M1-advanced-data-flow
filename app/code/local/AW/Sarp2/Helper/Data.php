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

class AW_Sarp2_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @param string $version
     *
     * @return bool
     */
    public function checkMageVersion($version = '1.4.0.0', $expression = '>=')
    {
        return version_compare(Mage::getVersion(), $version, $expression);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::helper('aw_sarp2/config')->isEnabled($store);
    }

    /**
     * copypaste from Mage_Core_Model_Locale::getDateFormatWithLongYear in mage 1.7.0.2
     */
    public function getDateFormatWithLongYear(Mage_Core_Model_Locale $locale)
    {
        return preg_replace(
            '/(?<!y)yy(?!y)/', 'yyyy',
            $locale->getTranslation(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, 'date')
        );
    }
}