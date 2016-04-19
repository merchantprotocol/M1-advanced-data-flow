<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_Rma
 * @version    1.0.0
 * @copyright  Copyright (c) 2013 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_Rma_Block_Adminhtml_Rma_Create_New_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (count(Mage::getSingleton('customer/session')->getCustomer()->getAddresses())) {
            $address = $form->addFieldset('address', array('legend' => Mage::helper('rma')->__('Select Address')));

            $address->addField('address_id', 'select', array(
                'label' => Mage::helper('rma')->__('Select Address from Address Book'),
                'options' => $this->getShippingAddresses(),
                'name' => 'address_id',
                'width' => '200px',
            ));
        } else {
            $contact = $form->addFieldset('contact', array('legend' => Mage::helper('rma')->__('Contact Information')));

            $contact->addField('first_name', 'text', array(
                'label' => Mage::helper('rma')->__('First Name'),
                'name' => 'first_name',
                'value' => $this->getOrder()->getShippingAddress()->getFirstname(),
            ));

            $contact->addField('second_name', 'text', array(
                'label' => Mage::helper('rma')->__('Second Name'),
                'name' => 'second_name',
                'value' => $this->getOrder()->getShippingAddress()->getLastname(),
            ));

            $contact->addField('company', 'text', array(
                'label' => Mage::helper('rma')->__('Company'),
                'name' => 'company',
                'value' => $this->getOrder()->getShippingAddress()->getCompany(),
            ));

            $contact->addField('telephone', 'text', array(
                'label' => Mage::helper('rma')->__('Telephone'),
                'name' => 'telephone',
                'value' => $this->getOrder()->getShippingAddress()->getTelephone(),
            ));

            $contact->addField('fax', 'text', array(
                'label' => Mage::helper('rma')->__('Fax'),
                'name' => 'fax',
                'value' => $this->getOrder()->getShippingAddress()->getFax(),
            ));

            $address = $form->addFieldset('address_form', array('legend' => Mage::helper('sales')->__('Address Information')));

            $address->addField('street', 'text', array(
                'label' => Mage::helper('rma')->__('Street Address'),
                'name' => 'street',
                'value' => $this->getOrder()->getShippingAddress()->getStreetFull(),
            ));

            $address->addField('city', 'text', array(
                'label' => Mage::helper('rma')->__('City'),
                'name' => 'city',
                'value' => $this->getOrder()->getShippingAddress()->getCity(),
            ));

            $address->addField('country', 'select', array(
                'label' => Mage::helper('rma')->__('Country'),
                'options' => $this->getContries(),
                'name' => 'country',
                'value' => $this->getOrder()->getShippingAddress()->getCountry(),
            ));

            $address->addField('region', 'text', array(
                'label' => Mage::helper('rma')->__('State/Province'),
                'name' => 'region',
                'value' => $this->getOrder()->getShippingAddress()->getRegion(),
            ));

            $address->addField('post_code', 'text', array(
                'label' => Mage::helper('rma')->__('Post/ZIP Code'),
                'name' => 'post_code',
                'value' => $this->getOrder()->getShippingAddress()->getPostcode(),
            ));
        }

        return parent::_prepareForm();
    }

    public function getContries() {
        $_countries = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
        if (count($_countries) > 0) {
            foreach ($_countries as $_country) {
                $countries[$_country['value']] = $_country['label'];
            }
            return $countries;
        }
    }

    public function getShippingAddresses() {
        $customer = $this->getCustomer();
        $data = array();
        foreach ($customer->getAddresses() as $address) {
            $data[$address->getEntityId()] = $address->format('oneline');
        }
        return $data;
    }

    public function getCustomer() {
        $customer = Mage::getModel('customer/customer')->load(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'))->getCustomerId());
        return $customer;
    }

    public function getOrder() {
        return Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
    }

}