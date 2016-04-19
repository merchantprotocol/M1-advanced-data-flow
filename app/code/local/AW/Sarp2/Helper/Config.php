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

class AW_Sarp2_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * "Subscription extension enabled" from system config
     */
    const GENERAL_ENABLED = 'aw_sarp2/settings/enabled';

    /**
     * "Subscription Engine" from system config
     */
    const GENERAL_SUBSCRIPTION_ENGINE = 'aw_sarp2/settings/subscription_engine';

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_ENABLED, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getSubscriptionEngine($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_SUBSCRIPTION_ENGINE, $store);
    }
}