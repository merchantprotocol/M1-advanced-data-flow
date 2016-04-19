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


class AW_Sarp2_Model_Resource_Subscription_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_sarp2/subscription');
    }

    /**
     * Join Product Name to collection
     *
     * @return AW_Sarp2_Model_Resource_Subscription_Collection
     */
    public function joinProductNames()
    {
        $storeId = Mage::app()->getStore()->getId();
        // eav_attribute
        $attribute = $this->getTable('eav/attribute');
        // eav_entity_type
        $entityType = $this->getTable('eav/entity_type');
        // catalog_product_entity_varchar
        $productVarchar = $this->getTable('catalog/product') . "_varchar";

        $attributeConditions = array(
            "_product_name_attribute.entity_type_id = _product_name_eavType.entity_type_id",
            "_product_name_attribute.attribute_code = 'name'",
        );

        $productVarcharConditions = array(
            "_product_name_value.attribute_id = _product_name_attribute.attribute_id",
            "_product_name_value.entity_id = main_table.product_id",
            "_product_name_value.store_id = {$storeId}",
        );

        $this->getSelect()
            ->joinLeft(
                array('_product_name_eavType' => $entityType),
                "_product_name_eavType.entity_type_code = 'catalog_product'",
                array()
            )
            ->joinLeft(
                array('_product_name_attribute' => $attribute),
                join(' AND ', $attributeConditions),
                array()
            )
            ->joinLeft(
                array('_product_name_value' => $productVarchar),
                join(' AND ', $productVarcharConditions),
                array('product_name' => 'value')
            )
        ;
        return $this;
    }

    public function joinSubscriptionTypes()
    {
        $subscriptionTypeId = 'GROUP_CONCAT(aw_sarp2_subscription_item.subscription_type_id SEPARATOR ",")';
        $this
            ->getSelect()
            ->joinLeft(
                array('aw_sarp2_subscription_item' => $this->getTable('aw_sarp2/subscription_item')),
                "main_table.entity_id = aw_sarp2_subscription_item.subscription_id",
                array('subscription_type_id' => $subscriptionTypeId)
            )
            ->group('main_table.entity_id')
        ;
        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'product_name':
                $this->getSelect()->where('_product_name_value.value LIKE ?', $condition['like']);
                return $this;
            case 'subscription_type_id':
                $this->getSelect()->having(
                    'FIND_IN_SET(?, GROUP_CONCAT(aw_sarp2_subscription_item.subscription_type_id SEPARATOR ","))',
                    (int)$condition['eq']
                );
                return $this;
            default:
                parent::addFieldToFilter('main_table.' . $field, $condition);
                return $this;
        }
    }

    public function getProductIds()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS)->columns('product_id');
        return $this->getConnection()->fetchCol($select);
    }
}