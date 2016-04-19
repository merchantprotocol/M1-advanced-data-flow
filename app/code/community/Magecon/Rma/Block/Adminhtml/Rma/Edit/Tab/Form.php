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
class Magecon_Rma_Block_Adminhtml_Rma_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $model = Mage::getModel('rma/address')->getCollection();
        foreach ($model as $item) {
            if ($item->getRmaId() == $this->getRma()->getRmaId())
                $_address = $item;
        }
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $contact = $form->addFieldset('contact_form', array('legend' => Mage::helper('rma')->__('Contact Information')));

        $contact->addField('first_name', 'text', array(
            'label' => Mage::helper('rma')->__('First Name'),
            'value' => $_address->getFirstName(),
            'name' => 'first_name',
            'required' => true,
        ));

        $contact->addField('second_name', 'text', array(
            'label' => Mage::helper('rma')->__('Second Name'),
            'value' => $_address->getSecondName(),
            'name' => 'second_name',
            'required' => true,
        ));

        $contact->addField('company', 'text', array(
            'label' => Mage::helper('rma')->__('Company'),
            'value' => $_address->getCompany(),
            'name' => 'company',
        ));

        $contact->addField('telephone', 'text', array(
            'label' => Mage::helper('rma')->__('Telephone'),
            'value' => $_address->getTelephone(),
            'name' => 'telephone',
            'required' => true,
        ));

        $contact->addField('fax', 'text', array(
            'label' => Mage::helper('rma')->__('Fax'),
            'value' => $_address->getFax(),
            'name' => 'fax',
        ));

        $address = $form->addFieldset('address_form', array('legend' => Mage::helper('rma')->__('Address Information')));

        $address->addField('street', 'text', array(
            'label' => Mage::helper('rma')->__('Street Address'),
            'value' => $_address->getStreet(),
            'name' => 'street',
            'required' => true,
        ));

        $address->addField('city', 'text', array(
            'label' => Mage::helper('rma')->__('City'),
            'value' => $_address->getCity(),
            'name' => 'city',
            'required' => true,
        ));

        $address->addField('country', 'select', array(
            'label' => Mage::helper('rma')->__('Country'),
            'options' => $this->getContries(),
            'value' => $_address->getCountry(),
            'name' => 'country',
            'required' => true,
        ));

        $address->addField('region', 'text', array(
            'label' => Mage::helper('rma')->__('State/Province'),
            'value' => $_address->getRegion(),
            'name' => 'region',
        ));

        $address->addField('post_code', 'text', array(
            'label' => Mage::helper('rma')->__('Post/ZIP Code'),
            'value' => $_address->getPostCode(),
            'name' => 'post_code',
            'required' => true,
        ));


        return parent::_prepareForm();
    }

    public function getRma() {
        return Mage::registry('sales_rma');
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

}