<?php

class Perception_Bannerpro_Block_Adminhtml_Bannerpro_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('bannerpro/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('bannerpro_form', array('legend'=>Mage::helper('bannerpro')->__('Banner Information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('bannerpro')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('bannerpro')->__('Banner Image'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled
            ));
        }
        
	  else {
	   $fieldset->addField('store_id', 'hidden', array(
	           'name'      => 'stores[]',
	           'value'     => Mage::app()->getStore(true)->getId()
	   ));
	   if(Mage::app()->getStore(true)->getId() != 1) {
	   		$model->setStoreId(Mage::app()->getStore(true)->getId());
	   	}
	  }
	  
	  $fieldset->addField('effects', 'select', array(
          'label'     => Mage::helper('bannerpro')->__('Banner Effects'),
          'name'      => 'effects',
		  'values'	  => Mage::getSingleton('bannerpro/system_config_source_effect')->toOptionArray()
      ));
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bannerpro')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannerpro')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannerpro')->__('Disabled'),
              ),
          ),
      ));
      
	 $fieldset->addField('sorting_order', 'text', array(
          'label'     => Mage::helper('bannerpro')->__('Sorting Order'),
          'required'  => false,
		  'style'     => 'width:50px;',
          'name'      => 'sorting_order',
      ));			

     $fieldset->addField('weblink', 'text', array(
	          'label'     => Mage::helper('bannerpro')->__('Website Url'),
	          'required'  => false,
	          'name'      => 'weblink',
			  'class'	  => 'validate-url',
	      ));
   
     $fieldset->addField('text', 'editor', array(
          'name'      => 'text',
          'label'     => Mage::helper('bannerpro')->__('Content'),
          'title'     => Mage::helper('bannerpro')->__('Content'),
          'style'     => 'width:450px; height:400px;',
          'wysiwyg'   => true,
          'required'  => false,
		  'config'    => Mage::getSingleton('bannerpro/wysiwyg_config')->getConfig()
      ));

     if ( Mage::getSingleton('adminhtml/session')->getBannerproData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerproData());
          Mage::getSingleton('adminhtml/session')->setBannerproData(null);
      } elseif ( Mage::registry('bannerpro_data') ) {
          $form->setValues(Mage::registry('bannerpro_data')->getData());
      }
      return parent::_prepareForm();
  }
}