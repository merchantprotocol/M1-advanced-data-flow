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
class Magestore_Customerattribute_Block_Adminhtml_Customeraddressattribute_Edit_Tab_General 
extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Form
     */
    protected function _prepareForm()
    {  
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getCustomeraddressattributeData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCustomeraddressattributeData();
            Mage::getSingleton('adminhtml/session')->setCustomeraddressattributeData(null);
        } elseif (Mage::registry('customeraddressattribute_data')) {
            $data = Mage::registry('customeraddressattribute_data')->getData();
        }       
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT); 
        $fieldset = $form->addFieldset('attribute_properties_form', array(
            'legend'=>Mage::helper('customerattribute')->__('General Configuration')
        ));
        $fieldset->addField('attribute_code', 'text', array(
            'label'       => Mage::helper('customerattribute')->__('Attribute Code'),
            'name'        => 'attribute_code',
            'note'        => Mage::helper('customerattribute')->__('Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a lette. The system will automatically convert CAPs to lowercase letters and remove spaces.'),
            'class'       => 'required-entry',
            'required'    => true,
            
        ));
        $fieldset->addField('frontend_input', 'select', array(
            'name'        => 'frontend_input',
            'label'       => Mage::helper('customerattribute')->__('Attribute Type'),
            'required'    => true,
            'values'      => Mage::helper('customerattribute')->getFrontendInputOptions(),
            'value'       => 'frontend_input',
        ));       
        $fieldset->addField('sort_order', 'text', array(
            'label'        => Mage::helper('customerattribute')->__('Sort Order'),
            'name'         => 'sort_order',
            'required'     => true,

        ));  
        $fieldset->addField('status', 'select', array(
            'label'       => Mage::helper('customerattribute')->__('Status'),
            'name'        => 'status',
            'values'      => Mage::getSingleton('customerattribute/status')->getOptionHash(),
            'required'    => true,
            'value'       => 'status',
        ));
         $fieldset->addField('is_required', 'select', array(
            'label'        => Mage::helper('customerattribute')->__('Required'),
            'name'         => 'is_required',
            'note'         => Mage::helper('customerattribute')->__('If yes, Customers have to fill in this attribute to keep processing'),
            'values'       => Mage::getSingleton('customerattribute/status')->getOptionBooleanHash(),
            'value'        => 'is_required',
        ));                
        
        /* Update Field for Frontend inpute */
        /* ---- Text Field ----*/       
        $fieldset->addField('min_text_length', 'text', array(
            'name'      => 'min_text_length',
            'label'     => Mage::helper('customerattribute')->__('Minimum Text Length'),
            'title'     => Mage::helper('customerattribute')->__('Minimum Text Length'),
            'class'     => 'validate-digits',
        ), 'input_validation');
        $fieldset->addField('max_text_length', 'text', array(
            'name'      => 'max_text_length',
            'label'     => Mage::helper('customerattribute')->__('Maximum Text Length'),
            'title'     => Mage::helper('customerattribute')->__('Maximum Text Length'),
            'class'     => 'validate-digits',
        ), 'min_text_length');
        
        /* ----Text Area---- */
              
        /* ----Date--- */                      
        $fieldset->addField('date_range_min', 'date', array(
            'name'      => 'date_range_min',
            'label'     => Mage::helper('customerattribute')->__('Minimal value'),
            'title'     => Mage::helper('customerattribute')->__('Minimal value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>$dateFormatIso, //$helper->getDateFormat(),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ), 'default_value_date');

        $fieldset->addField('date_range_max', 'date', array(
            'name'      => 'date_range_max',
            'label'     => Mage::helper('customerattribute')->__('Maximum value'),
            'title'     => Mage::helper('customerattribute')->__('Maximum value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>$dateFormatIso, //$helper->getDateFormat()
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ), 'date_range_min');
        
        /* --- Yes/No -- */       
        
        /*---File Attachment --- */
        $fieldset->addField('max_file_size', 'text', array(
            'name'      => 'max_file_size',
            'label'     => Mage::helper('customerattribute')->__('Maximum File Size (bytes)'),
            'title'     => Mage::helper('customerattribute')->__('Maximum File Size (bytes)'),
            'class'     => 'validate-digits',
        ), 'max_text_length');

        $fieldset->addField('file_extensions', 'text', array(
            'name'      => 'file_extensions',
            'label'     => Mage::helper('customerattribute')->__('File Extensions'),
            'title'     => Mage::helper('customerattribute')->__('File Extensions'),
            'note'      => Mage::helper('customerattribute')->__('Comma separated'),
        ), 'max_file_size');
        
        /* -- Image -- */
        $fieldset->addField('max_image_width', 'text', array(
            'name'      => 'max_image_width',
            'label'     => Mage::helper('customerattribute')->__('Maximum Image Width (px)'),
            'title'     => Mage::helper('customerattribute')->__('Maximum Image Width (px)'),
            'class'     => 'validate-digits',
        ), 'max_file_size');

        $fieldset->addField('max_image_heght', 'text', array(
            'name'      => 'max_image_heght',
            'label'     => Mage::helper('customerattribute')->__('Maximum Image Height (px)'),
            'title'     => Mage::helper('customerattribute')->__('Maximum Image Height (px)'),
            'class'     => 'validate-digits',
        ), 'max_image_width');
       $fieldset->addField('input_validation', 'select', array(
            'name'      => 'input_validation',
            'label'     => Mage::helper('customerattribute')->__('Input Validation'),
            'title'     => Mage::helper('customerattribute')->__('Input Validation'),
            'values'    => array('' => Mage::helper('customerattribute')->__('None')),           
        ), 'default_value_textarea');     
        $fieldset = $form->addFieldset('attribute_config_form', array(
            'legend'=>Mage::helper('customerattribute')->__('Display Configuration')
        ));
        
        $fieldset->addField('store_id', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('Store View'),
            'required'     => true,
            'name'         => 'store_id',
            'values'       =>  Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'value'        =>  Mage::helper('customerattribute')->getValueShowOnStore($data),
        ));
        
        $fieldset->addField('display_on_frontend', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('Forms to Use In Frontend'),
            'name'         => 'display_on_frontend',
            'values'       => Mage::helper('customerattribute')->getShowAddressOnFrontend(),
            'value'        => Mage::helper('customerattribute')->getValueAddressShowOnFrontend($data),
            'after_element_html' =>'<script>
                                    var b = $("frontend_input"); 
                                    var f=$("attribute_code");
                                    if(f.value!=0 && (b.value=="file" || b.value=="image")){
                                    var a= $("display_on_frontend");
                                    a.options[1].disabled="disabled";}
                                    b.observe("change",function(){
                                    if(b.selectedIndex == 6 || b.selectedIndex == 7){                                         
                                        $("display_on_frontend").options[1].selected = false;
                                        $("display_on_frontend").options[1].disabled = true;
                                        $("is_required").options[1].selected = true;
                                        $("is_required").disabled = true;
                                    }else{                                        
                                        $("display_on_frontend").options[1].disabled=false;
                                        $("is_required").disabled = false;
                                    }
                                    });                                   
                                    </script>',
        ));
        $fieldset->addField('display_on_backend', 'select', array(
            'label'        => Mage::helper('customerattribute')->__('Show on Backend'),
            'name'         => 'display_on_backend',
            'values'       => Mage::getSingleton('customerattribute/status')->getOptionBooleanHash(),
            'value'        => 'display_on_backend',
        ));
               
       
       $inputTypeProp = Mage::helper('customerattribute')->getAttributeInputTypes($data['frontend_input']);
       if ($inputTypeProp['validate_filters']) {
                $filterTypes = Mage::helper('customerattribute')->getAttributeValidateFilters();
                $values = $form->getElement('input_validation')->getValues();
                foreach ($inputTypeProp['validate_filters'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }    
                $form->getElement('input_validation')->setValues($values);
            }
        $condition=unserialize($data['validate_rules']);
        foreach($condition as $label=> $value)
        {
          $data[$label]=$value;
         }
        $form->getElement('input_validation')->setValue($data['input_validation']);               
        $data['display_on_frontend'] = Mage::helper('customerattribute')->getValueAddressShowOnFrontend($data);              
        $form->setValues($data);   
        if ($data['attribute_id']) {
            $elements = array();
            if ($data['is_custom'] == 0) {
                $elements = array('sort_order', 'frontend_input','status','store_id',
                    'display_on_frontend','display_on_backend','is_required', 'attribute_code');
            }
            else{
                 $elements = array('frontend_input','attribute_code');
            }
        }
        foreach ($elements as $elementId) {
            $form->getElement($elementId)->setDisabled(1);
        }   
         if($data['frontend_input'] == 'file'||$data['frontend_input'] == 'image')
                $form->getElement('is_required')->setDisabled(1);
        return parent::_prepareForm();
    }
}