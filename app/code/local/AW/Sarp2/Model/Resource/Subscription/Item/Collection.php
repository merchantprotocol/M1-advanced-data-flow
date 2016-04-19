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


class AW_Sarp2_Model_Resource_Subscription_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_joinedWithSubscriptionType = false;

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_sarp2/subscription_item');
    }

    /**
     * @return AW_Sarp2_Model_Resource_Subscription_Item_Collection
     */
    public function joinSubscriptionTypeData()
    {
        if ($this->_joinedWithSubscriptionType) {
            return $this;
        }
        $this->_joinedWithSubscriptionType = true;
        $subscriptionTypeTable = $this->getTable('aw_sarp2/subscription_type');
        $this->getSelect()
            ->joinLeft(
                array('subscription_type' => $subscriptionTypeTable),
                'main_table.subscription_type_id = subscription_type.entity_id',
                array()
            )
        ;
        return $this;
    }

    /**
     * @param AW_Sarp2_Model_Subscription|integer
     *
     * @return AW_Sarp2_Model_Resource_Subscription_Item_Collection
     */
    public function addSubscriptionFilter($subscription)
    {
        if ($subscription instanceof AW_Sarp2_Model_Subscription) {
            $subscriptionId = $subscription->getId();
        } elseif (is_numeric($subscription)) {
            $subscriptionId = $subscription;
        } else {
            return $this;
        }
        $this->addFieldToFilter('subscription_id', $subscriptionId);
        return $this;
    }

    public function addSubscriptionTypeVisibilityFilter()
    {
        $this->joinSubscriptionTypeData();
        $this->addFieldToFilter('subscription_type.is_visible', array('eq' => 1));
        return $this;
    }

    public function addSubscriptionTypeStoreIdFilter($storeId)
    {
        $this->joinSubscriptionTypeData();
        $finsetPartAllStores = $this->_getConditionSql('subscription_type.store_ids', array('finset' => 0));
        $finsetPartCurrentStore = $this->_getConditionSql('subscription_type.store_ids', array('finset' => $storeId));
        $this->getSelect()->where($finsetPartAllStores . ' OR ' . $finsetPartCurrentStore);
        return $this;
    }

    public function addEngineCodeFilter($code)
    {
        $this->joinSubscriptionTypeData();
        $this->addFieldToFilter('subscription_type.engine_code', array('eq' => $code));
        return $this;
    }

    /**
     * @return AW_Sarp2_Model_Resource_Subscription_Item_Collection
     */
    public function addSortOrder()
    {
        $this->addOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC);
        return $this;
    }
}