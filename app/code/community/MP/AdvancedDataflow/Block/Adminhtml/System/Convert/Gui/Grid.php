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
 * Convert profiles grid
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid 
{
    /**
     * Retrieve advanced dataflow helper
     * 
     * @return MP_AdvancedDataflow_Helper_Data
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
     * @return MP_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $entityType = $this->getColumn('entity_type');
        $entityType->setOptions($this->_getEntityTypes());
        return $this;
    }
}
