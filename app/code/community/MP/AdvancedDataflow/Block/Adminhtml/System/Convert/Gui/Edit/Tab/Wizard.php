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
 * Convert profile edit wizard tab
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Edit_Tab_Wizard 
    extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('advanceddataflow/system/convert/profile/wizard.phtml');
    }
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
     * Get attributes
     * 
     * @param string $entityType
     * @return MP_AdvancedDataflow_Block_Adminhtml_System_Convert_Gui_Edit_Tab_Wizard
     */
    public function getAttributes($entityType)
    {
        $helper = $this->_getHelper();
        if (in_array($entityType, array('order'))) {
            switch ($entityType) {
                case 'order': 
                    $attributes = Mage::getSingleton('advanceddataflow/sales_convert_parser_order')->getExternalAttributes();
                    break;
            }
            array_splice($attributes, 0, 0, array('' => $helper->__('Choose an attribute')));
            $this->_attributes[$entityType] = $attributes;
        }
        return parent::getAttributes($entityType);
    }
    /**
     * Get order status filter options
     * 
     * return array
     */
    public function getOrderStatusFilterOptions()
    {
        $helper = $this->_getHelper();
        $options = Mage::getResourceModel('sales/order_status_collection')->toOptionArray();
        array_unshift($options, array('value' => '', 'label' => $helper->__('All Statuses')));
        return $options;
    }
    /**
     * Get order shipping methods filter options
     * 
     * return array
     */
    public function getOrderShippingMethodsFilterOptions()
    {
        $helper = $this->_getHelper();
        $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')->toOptionArray();
        array_shift($options);
        array_unshift($options, array('value' => '', 'label' => $helper->__('All Shipping Methods')));
        return $options;
    }
	/**
     * Get currency filter options
     * 
     * return array
     */
    public function getCurrencyFilterOptions()
    {
        $helper = $this->_getHelper();
        $options = Mage::app()->getLocale()->getOptionCurrencies();
        array_unshift($options, array('value' => '', 'label' => $helper->__('All Currencies')));
        return $options;
    }
}
