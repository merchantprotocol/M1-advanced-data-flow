<?php
/**
 * Mage Plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mage Plugins Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://mageplugins.net/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mageplugins@gmail.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to https://www.mageplugins.net for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2018 Mage Plugins Inc. and affiliates (https://mageplugins.net/)
 * @license    https://mageplugins.net/commercial-license/  Mage Plugins Commercial License (MPCL 1.0)
 */

/**
 * Order item eav attribute collection resource
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Mysql4_Sales_Order_Item_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
    protected $_entityTypeCode = 'sales_order_item';
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
