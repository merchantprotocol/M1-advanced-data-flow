<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Js
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve allowed Input Validate Filters in JSON format
     *
     * @return string
     */
    public function getValidateFiltersJson()
    {
        return Mage::helper('core')->jsonEncode(Mage::helper('customerattribute')->getAttributeValidateFilters());
    }

    /**
     * Retrieve allowed Input Filter Types in JSON format
     *
     * @return string
     */
    public function getFilteTypesJson()
    {
        return Mage::helper('core')->jsonEncode(Mage::helper('customerattribute')->getAttributeFilterTypes());
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return Mage::helper('customerattribute')->getAttributeInputTypes();
    }
}
