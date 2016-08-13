<?php
/**
 * Merchant Protocol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Merchant Protocol Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://merchantprotocol.com/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@merchantprotocol.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.merchantprotocol.com for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2016 Merchant Protocol LLC. and affiliates (https://merchantprotocol.com/)
 * @license    https://merchantprotocol.com/commercial-license/  Merchant Protocol Commercial License (MPCL 1.0)
 */

/**
 * Order eav attribute collection resource
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Mysql4_Sales_Order_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
    protected $_entityTypeCode = 'sales_order';
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
     * @return Innoexts_AdvancedDataflow_Model_Mysql4_Sales_Order_Entity_Attribute_Collection
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
            ->order('main_table.attribute_code ASC');
        return $this;
    }
}