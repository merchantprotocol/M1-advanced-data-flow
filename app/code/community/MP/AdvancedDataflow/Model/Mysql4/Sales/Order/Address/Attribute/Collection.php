<?php
/**
 * Mage Plugins, Inc
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://mageplugins.net/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mageplugins@gmail.com so we can send you a copy immediately.
 * 
 * @category    MP
 * @package     MP_AdvancedDataflow
 * @copyright   Copyright (c) 2011-2018 Mage Plugins, Inc (http://mageplugins.net)
 * @license     https://mageplugins.net/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Order address eav attribute collection resource
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Mysql4_Sales_Order_Address_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
{
    /**
     * Attribute Entity Type Filter
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'sales_order_address';
    /**
     * Return customer entity type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($this->_entityTypeCode);
        }
        return $this->_entityType;
    }
    /**
     * Initialize collection select
     *
     * @return MP_AdvancedDataflow_Model_Mysql4_Sales_Order_Entity_Attribute_Collection
     */
    protected function _initSelect()
    {
        $entityType     = $this->getEntityType();
        $mainDescribe   = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns    = array();
        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $mainColumns)
            ->where('main_table.entity_type_id = ?', $entityType->getId())
            ->order('main_table.attribute_id ASC');
        return $this;
    }
}