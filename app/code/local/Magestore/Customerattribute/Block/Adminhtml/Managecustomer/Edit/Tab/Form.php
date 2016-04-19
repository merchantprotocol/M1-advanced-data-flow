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

/**
 * Customerattribute Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function getCustomtabInfo() {
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $entityTypeId = $setup->getEntityTypeId('customer');

        $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
        $customerAttribute = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId);
        $customerAttribute->getSelect()
                ->join(array('table_attribute' => $tbl_faq_item), 'main_table.attribute_id=table_attribute.attribute_id');
        $customerAttribute->addFieldToFilter('table_attribute.is_custom', 1);

        // custom attribute filter
        $customerTypeValue = Mage::registry('current_customer')->getSelectCustomerType();
        $customerTypeText = Mage::helper('idpas400')->getAttributeOptionText($customerTypeValue);
        $customerAttribute = Mage::helper('idpas400')->filterAttributesCollection($customerAttribute, $customerTypeText);

        return $customerAttribute;
    }

    public function getCustomerAttribute() {
        $id = $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer')->load($id);
        return $customer;
    }

    public function getOptions($attributeId) {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeId)
                ->setPositionOrder('desc', true)
                ->load();
        $options = array();
        $options[] = array();
        foreach ($optionCollection as $option) {
            $options[] = array(
                'value' => $option->getOptionId(),
                'label' => $option->getValue()
            );
        }
        return $options;
    }

    public function getValueOprions($valueAttribute) {
        $valueAttribute = explode(',', $valueAttribute);
        return $valueAttribute;
    }

    public function getTabLabel() {
        return $this->__('Additional information');
    }

    public function getTabTitle() {
        return $this->__('Attribute Tab');
    }

    public function canShowTab() {
        $customer = Mage::registry('current_customer');
        return (bool) $customer->getId();
    }

    public function isHidden() {
        return false;
    }

    public function getAfter() {
        return 'tags';
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('tabCustomerattribute');
        $this->setForm($form);
        $customerAttributes = $this->getCustomtabInfo();
        $customer = $this->getCustomerAttribute();

        $fieldset = $form->addFieldset('customerattribute_form1', array(
            'legend' => Mage::helper('customerattribute')->__('Attribute Properties')
        ));
        $fieldset->addType('file', Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'));
        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'));
        foreach ($customerAttributes as $customerAttribute) {

            if (!$customerAttribute->getIsVisible())
                continue;

            if ($customerAttribute->getFrontendInput() == 'date') {
                $fieldset->addField($customerAttribute->getAttributeCode(), $customerAttribute->getFrontendInput(), array(
                    'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                    'required' => $customerAttribute->getIsRequired(),
                    'name' => $customerAttribute->getAttributeCode(),
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                    'value' => $customer->getData($customerAttribute->getAttributeCode()),
                ));
            } else if ($customerAttribute->getFrontendInput() == 'boolean') {
                $fieldset->addField($customerAttribute->getAttributeCode(), 'select', array(
                    'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                    'required' => $customerAttribute->getIsRequired(),
                    'name' => $customerAttribute->getAttributeCode(),
                    'values' => Mage::getSingleton('customerattribute/status')->getOptionBoolean(),
                    'value' => $customer->getData($customerAttribute->getAttributeCode()),
                ));
            } else if ($customerAttribute->getFrontendInput() == 'select' || $customerAttribute->getFrontendInput() == 'multiselect') {
                $fieldset->addField($customerAttribute->getAttributeCode(), $customerAttribute->getFrontendInput(), array(
                    'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                    'required' => $customerAttribute->getIsRequired(),
                    'name' => $customerAttribute->getAttributeCode(),
                    'values' => $this->getOptions($customerAttribute->getAttributeId()),
                    'value' => $this->getValueOprions($customer->getData($customerAttribute->getAttributeCode())),
                ));
            } else if ($customerAttribute->getFrontendInput() == 'file') {
                if ($customer->getData($customerAttribute->getAttributeCode()) != null) {
                    $fieldset->addField($customerAttribute->getAttributeCode(), 'file', array(
                        'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                        'required' => $customerAttribute->getIsRequired(),
                        'name' => $customerAttribute->getAttributeCode(),
                        'value' => 'customer/' . $customer->getData($customerAttribute->getAttributeCode()),
                    ));
                } else {
                    $fieldset->addField($customerAttribute->getAttributeCode(), 'file', array(
                        'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                        'required' => $customerAttribute->getIsRequired(),
                        'name' => $customerAttribute->getAttributeCode(),
                    ));
                }
            } else if ($customerAttribute->getFrontendInput() == 'image') {
                if ($customer->getData($customerAttribute->getAttributeCode()) != null) {

                    $fieldset->addField($customerAttribute->getAttributeCode(), 'image', array(
                        'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                        'required' => $customerAttribute->getIsRequired(),
                        'name' => $customerAttribute->getAttributeCode(),
                        'value' => 'customer/' . $customer->getData($customerAttribute->getAttributeCode()),
                    ));
                } else {
                    $fieldset->addField($customerAttribute->getAttributeCode(), 'image', array(
                        'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                        'required' => $customerAttribute->getIsRequired(),
                        'name' => $customerAttribute->getAttributeCode(),
                    ));
                }
            } else {
                $fieldset->addField($customerAttribute->getAttributeCode(), $customerAttribute->getFrontendInput(), array(
                    'label' => Mage::helper('customerattribute')->__($customerAttribute->getFrontendLabel()),
                    'required' => $customerAttribute->getIsRequired(),
                    'name' => $customerAttribute->getAttributeCode(),
                    'value' => $customer->getData($customerAttribute->getAttributeCode()),
                ));
            }
        }
        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes() {
        return array(
            'file' => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
        );
    }

}
