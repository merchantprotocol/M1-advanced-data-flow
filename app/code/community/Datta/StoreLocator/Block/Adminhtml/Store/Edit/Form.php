<?php
/**
 * Magento 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *  
 * 
 * @category    Datta
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  2nd Dec,2013 2:50pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Manage store locator edit form 
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Datta_StoreLocator_Block_Adminhtml_Store_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */                         
    public function __construct()
    {
        parent::__construct();

        $this->setId('store_form');
        $this->setTitle($this->__('Store Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('datta_storelocator');

        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data')
        );

        $fieldset = $form->addFieldset('datta_storelocator_form', array(
            'legend'    => Mage::helper('checkout')->__('Store Information')
        ));


        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }

        $fieldset->addField('created_time', 'hidden', array(
            'name' => 'created_time',
        ));

        $fieldset->addField('update_time', 'hidden', array(
            'name' => 'update_time',
        ));


       

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('datta_storelocator')->__('Store View'),
                'title'     => Mage::helper('datta_storelocator')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Store Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('store_url', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Website URL'),
            'required'  => false,
            'name'      => 'store_url',
        ));
        
        $fieldset->addField('image', 'image', array(
            'label'     => Mage::helper('datta_storelocator')->__('Store Image'),
            'required'  => false,
            'name'      => 'image',
        ));
        
        $fieldset->addField('address', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Address'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'address',
            'onchange'  => 'getLatLng()',
        ));     

        $fieldset->addField('city', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('City'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'city',
            'onchange'    => 'getLatLng()',
        ));

        $fieldset->addField('zipcode', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Zip Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'zipcode',
        ));

        $countryList = Mage::getModel('directory/country')->getCollection()->toOptionArray();
        
        $fieldset->addField('country_id', 'select', array(
            'label'     => Mage::helper('datta_storelocator')->__('Country'),
            'name'      => 'country_id',
            'title'     => 'country',
            'values'    => $countryList,
            'onchange'  => 'getLatLng()',
        ));

        $fieldset->addField('phone', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Phone No'),
            'name'      => 'phone',
        ));

        $fieldset->addField('fax', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Fax No'),
            'name'      => 'fax',
        )); 

        $fieldset->addField('lat', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Latitude'),
            'required'  => true,
            'name'      => 'lat',
        ));

        $fieldset->addField('long', 'text', array(
            'label'     => Mage::helper('datta_storelocator')->__('Longitude'),
            'required'  => true,
            'name'      => 'long',
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'label'     => Mage::helper('datta_storelocator')->__('Short Description'),
            'name'      => 'description',
        ));       
        
        $fieldset->addField('marker', 'image', array(
                'label'     => Mage::helper('datta_storelocator')->__('Map Marker Image'),
                'required'  => false,
                'name'      => 'marker',
        ));
         $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('datta_storelocator')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('datta_storelocator')->__('Enabled'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('datta_storelocator')->__('Disabled'),
                ),
            ),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}