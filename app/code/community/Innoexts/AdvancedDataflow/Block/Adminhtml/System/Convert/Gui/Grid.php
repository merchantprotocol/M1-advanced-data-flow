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
 * Convert profiles grid
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid 
{
    /**
     * Retrieve advanced dataflow helper
     * 
     * @return Innoexts_AdvancedDataflow_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('advanceddataflow');
    }
    /**
     * Get entity types
     * 
     * @return array
     */
    protected function _getEntityTypes()
    {
        $helper = $this->_getHelper();
        return array('product' => $helper->__('Products'), 'customer' => $helper->__('Customers'), 'order' => $helper->__('Orders'));
    }
    /**
     * Prepare columns
     * 
     * @return Innoexts_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $entityType = $this->getColumn('entity_type');
        $entityType->setOptions($this->_getEntityTypes());
        return $this;
    }
}
