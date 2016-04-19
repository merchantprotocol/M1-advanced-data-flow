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
class Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
		//zend_debug::dump($attribute);die('dddddd');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getCustomerattributeData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCustomerattributeData();
            Mage::getSingleton('adminhtml/session')->setCustomerattributeData(null);
        } elseif (Mage::registry('customerattribute_data')) {
            $data = Mage::registry('customerattribute_data')->getData();
        }
		
		$fieldset = $form->addFieldset('customerattribute_form1', array(
            'legend'=>Mage::helper('customerattribute')->__('General Configuration')
        ));
		$fieldset->addField('is_custom', 'hidden', array(
            'required'    => false,
            'name'        => 'is_custom',		
			'value'		=> 'is_custom',
        ));

        $fieldset->addField('attribute_code', 'text', array(
            'label'        => Mage::helper('customerattribute')->__('Attribute Code'),
			'required'    => true,
            'name'        => 'attribute_code',
			'note'        => Mage::helper('customerattribute')->__('Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a lette. The system will automatically convert CAPs to lowercase letters and remove spaces.'),
			'value'		=> 'attribute_code',
            
        ));

        $fieldset->addField('frontend_input', 'select', array(
            'name'        => 'frontend_input',
            'label'        => Mage::helper('customerattribute')->__('Attribute Type'),
            'required'    => true,
			'values'    => Mage::helper('customerattribute')->getFrontendInputOptions(),
			'value'		=> 'frontend_input',
        ));
		
		$fieldset->addField('sort_order', 'text', array(
            'label'        => Mage::helper('customerattribute')->__('Sort Order'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'sort_order',
        ));
		
		$fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('customerattribute')->__('Status'),
            'required'    => false,
            'name'        => 'status',
			'values'    => Mage::getSingleton('customerattribute/status')->getOptionArray(),
			'value'		=> 'status',
        ));
	$fieldset->addField('is_required', 'select', array(
            'label'        => Mage::helper('customerattribute')->__('Required'),
            'name'        => 'is_required',
                    'note'         => Mage::helper('customerattribute')->__('If yes, Customers have to fill in this attribute to keep processing'),
			'values'    => Mage::getSingleton('customerattribute/status')->getOptionBoolean(),
			'value'		=> 'is_required',
        ));
		
	
        $fieldset->addField('min_text_length', 'text', array(
            'name'      => 'min_text_length',
            'label'     => Mage::helper('customerattribute')->__('Minimum Characters Customers must fill in'),
            'title'     => Mage::helper('customerattribute')->__('Minimum Characters Customers must fill in'),
            'class'     => 'validate-digits',
        ), 'input_validation');
        $fieldset->addField('max_text_length', 'text', array(
            'name'      => 'max_text_length',
            'label'     => Mage::helper('customerattribute')->__('Maximum Characters Customers must fill in'),
            'title'     => Mage::helper('customerattribute')->__('Maximum Characters Customers must fill in'),
            'class'     => 'validate-digits',
        ), 'min_text_length');
        
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		
        $fieldset->addField('date_range_min', 'date', array(
            'name'      => 'date_range_min',
            'label'     => Mage::helper('customerattribute')->__('Minimal value'),
            'title'     => Mage::helper('customerattribute')->__('Minimal value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>$dateFormatIso, 
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ), 'default_value_date');

        $fieldset->addField('date_range_max', 'date', array(
            'name'      => 'date_range_max',
            'label'     => Mage::helper('customerattribute')->__('Maximum value'),
            'title'     => Mage::helper('customerattribute')->__('Maximum value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>$dateFormatIso, 
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ), 'date_range_min');
        
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
            'values'    => array('' => Mage::helper('customerattribute')->__('None'))
        ), 'default_value_textarea');	
        $fieldset = $form->addFieldset('customerattribute_form', array(
            'legend'=>Mage::helper('customerattribute')->__('Display Configuration')
        ));

        $fieldset->addField('store_view', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('Store View'),
            'required'    => true,
            'name'        => 'store_view',
			'values'       =>  Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			'value'			=>Mage::helper('customerattribute')->getValueShowOnStore($data),
        ));
		$fieldset->addField('customer_group', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('Customer Groups'),
            'required'    => true,
            'name'        => 'customer_group',
			'values'       =>  Mage::helper('customerattribute')->getGroups(),
			'value'		=>Mage::helper('customerattribute')->getValueShowOnGroup($data),
        ));
        $fieldset->addField('display_on_frontend', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('In Frontend'),
            'name'        => 'display_on_frontend',
            'values'    => Mage::helper('customerattribute')->getShowOnFrontend(),
			'value'		=> Mage::helper('customerattribute')->getValueShoOnFrontend($data),
			'after_element_html' =>'<script>
                                    var b = $("frontend_input"); 
                                    var f=$("attribute_code");
									var a= $("display_on_frontend");
                                    if(f.value!=0 && (b.value=="file" || b.value=="image")){
                                    a.options[2].disabled="disabled";
									a.options[3].disabled="disabled";
									}
									if(!$("display_on_frontend").options[2].selected)
									$("display_on_frontend").options[3].disabled="disabled";
									$($("display_on_frontend")).observe("change",function(){
										if($("display_on_frontend").options[2].selected)
										{
											$("display_on_frontend").options[3].disabled=false;
										}
										else
										{
											$("display_on_frontend").options[3].selected = false;
											$("display_on_frontend").options[3].disabled="disabled";
										}
									});
									
									 $("frontend_input").observe("change",function(){
                                    if($("frontend_input").selectedIndex == 6 || $("frontend_input").selectedIndex == 7){                                        
                                        $("display_on_frontend").options[2].selected = false;
                                        $("display_on_frontend").options[2].disabled = true;
										$("display_on_frontend").options[3].selected = false;
                                        $("display_on_frontend").options[3].disabled = true;
										$("display_on_backend").options[2].selected = false;
										$("display_on_backend").options[2].disabled = true;
                                        $("is_required").options[1].selected = true;
                                        $("is_required").disabled = true;
                                    }else{                                        
                                        $("display_on_frontend").options[2].disabled=false;
										if($("display_on_frontend").options[2].selected)
										$("display_on_frontend").options[3].disabled=false;
										$("display_on_backend").options[2].disabled = false;
                                        $("is_required").disabled = false;
                                    }
                                    });
                                    </script>',
        ));
		
		$fieldset->addField('display_on_backend', 'multiselect', array(
            'label'        => Mage::helper('customerattribute')->__('In Backend'),
            'name'        => 'display_on_backend',
            'values'    => Mage::helper('customerattribute')->getShowOnBackend(),
			'value'		=> Mage::helper('customerattribute')->getValueShoOnBackend($data),
			'after_element_html' =>'<script>
			var b=$("is_custom");
			var c = $("frontend_input"); 
			var f=$("attribute_code");
			if((b.value==0&&f.value!=0)||c.value=="file" || c.value=="image"){
			var a= $("display_on_backend");
			a.options[2].disabled="disabled";}
			</script>',
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
		$data['display_on_frontend'] =Mage::helper('customerattribute')->getValueShoOnFrontend($data);
		$data['display_on_backend'] =Mage::helper('customerattribute')->getValueShoOnBackend($data);
		$data['customer_group'] =Mage::helper('customerattribute')->getValueShowOnGroup($data);
		$data['store_view'] =Mage::helper('customerattribute')->getValueShowOnStore($data);
		
		$form->getElement('input_validation')->setValue($data["input_validation"]);
        $form->setValues($data);
		
		
		if ($data['attribute_id']) {
            $elements = array();
            if ($data['is_custom'] == 0) {
                $elements = array('sort_order', 'frontend_input','status','store_view',
                    'display_on_frontend','customer_group','is_required', 'attribute_code');
            }
            else{
                 $elements = array('frontend_input','attribute_code');
            }
        }
        foreach ($elements as $elementId) {
            $form->getElement($elementId)->setDisabled(1);
        }

        return parent::_prepareForm();
    }
}