<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
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