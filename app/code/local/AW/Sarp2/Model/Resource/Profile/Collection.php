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


class AW_Sarp2_Model_Resource_Profile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_sarp2/profile');
    }

    /**
     * Callback function that filters collection by field "Status" from grid
     *
     * @param AW_Sarp2_Model_Resource_Profile_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column    $column
     *
     * @return $this
     */
    public function addStatusFilterCallback($collection, $column)
    {
        $filterValue = $column->getFilter()->getCondition();
        $globalStatuses = Mage::getModel('aw_sarp2/source_profile_status')->getGlobalStatuses();
        if (in_array($filterValue, array_keys($globalStatuses))) {
            $conditions = array();
            foreach ($globalStatuses[$filterValue] as $key => $value) {
                $conditions[] = $this->_quoteInto($value, $key);
            }
            $collection->getSelect()->where(implode(' OR ', $conditions));
        } else {
            list($engineCode, $status) = explode(AW_Sarp2_Model_Source_Profile_Status::STATUS_DELIMETER, $filterValue);
            $collection->getSelect()->where($this->_quoteInto($status, $engineCode));
        }
        return $this;
    }

    /**
     * @param $status
     *
     * @return AW_Sarp2_Model_Resource_Profile_Collection
     */
    public function addStatusFilter($status)
    {
        $globalStatuses = Mage::getModel('aw_sarp2/source_profile_status')->getGlobalStatuses();
        if (!$status) {
            return $this;
        } elseif (array_key_exists($status, $globalStatuses)) {
            $conditions = array();
            foreach ($globalStatuses[$status] as $key => $value) {
                $conditions[] = $this->_quoteInto($value, $key);
            }
            $this->getSelect()->where(implode(' OR ', $conditions));
        }
        return $this;
    }

    /**
     * @param $status
     * @param $engine
     *
     * @return string
     */
    private function _quoteInto($status, $engine)
    {
        $statusWhere = $this->getSelect()->getAdapter()->quoteInto('`main_table`.`status` = ?', $status);
        $engineWhere = $this->getSelect()->getAdapter()->quoteInto(
            '`main_table`.`subscription_engine_code` = ?', $engine
        );
        return "({$statusWhere} AND {$engineWhere})";
    }

    /**
     * @return AW_Sarp2_Model_Resource_Profile_Collection
     */
    public function addCustomerNameToSelect()
    {
        $firstname = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');
        $lastname = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname');

        $firstnameConditions = array(
            "customer_firstname_table.entity_id = main_table.customer_id",
            "customer_firstname_table.attribute_id = {$firstname->getAttributeId()}"
        );

        $lastnameConditions = array(
            "customer_lastname_table.entity_id = main_table.customer_id",
            "customer_lastname_table.attribute_id = {$lastname->getAttributeId()}"
        );

        $this->getSelect()
            ->joinLeft(
                array('customer_firstname_table' => $firstname->getBackend()->getTable()),
                join(' AND ', $firstnameConditions),
                array()
            )
            ->joinLeft(
                array('customer_lastname_table' => $lastname->getBackend()->getTable()),
                join(' AND ', $lastnameConditions),
                array()
            )
        ;
        $this->getSelect()->columns(
            array('customer_fullname' => 'CONCAT(customer_firstname_table.value, " ", customer_lastname_table.value)')
        );
        return $this;
    }

    /**
     * @return AW_Sarp2_Model_Resource_Profile_Collection
     */
    public function addLastOrderDataToSelect()
    {
        $orderResource = Mage::getModel('sales/order')->getResource();
        $this->getSelect()->joinLeft(
            array('order_table' => $orderResource->getTable('sales/order')),
            'order_table.entity_id = main_table.last_order_id',
            array(
                'last_order_increment_id'     => 'order_table.increment_id',
                'last_order_base_grand_total' => 'order_table.base_grand_total',
            )
        );
        return $this;
    }

    /**
     * @param array|string $field
     * @param null         $condition
     *
     * @return AW_Sarp2_Model_Resource_Profile_Collection|Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'customer_fullname') {
            $this->getSelect()->where(
                'CONCAT(customer_firstname_table.value, " ", customer_lastname_table.value) LIKE ?', $condition['like']
            );
            return $this;
        }
        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * @param $customer = Mage_Customer_Model_Customer|int
     *
     * @return AW_Sarp2_Model_Resource_Profile_Collection
     */
    public function addCustomerFilter($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        } elseif (is_numeric($customer)) {
            $customerId = $customer;
        } else {
            return $this;
        }
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }

    /**
     * @param $type = AW_Sarp2_Model_Subscription_Type|int
     *
     * @return AW_Sarp2_Model_Resource_Profile_Collection
     */
    public function addSubscriptionTypeFilter($type)
    {
        if ($type instanceof AW_Sarp2_Model_Subscription_Type) {
            $typeId = $type->getId();
        } elseif (is_numeric($type)) {
            $typeId = $type;
        } else {
            return $this;
        }
        $this->addFieldToFilter('subscription_type_id', $typeId);
        return $this;
    }

    public function addSortByCreatedAt()
    {
        $this->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);
        return $this;
    }
}