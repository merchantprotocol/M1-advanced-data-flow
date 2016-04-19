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
 * Customerattribute Model
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */

//require "AW/Sarp2/Model/Checkout/Type/Onepage.php";
//class Magestore_Customerattribute_Model_Type_Onepage extends AW_Sarp2_Model_Checkout_Type_Onepage {

class Magestore_Customerattribute_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

     public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        //save for event  --- 
        $checkoutsission = Mage::getSingleton('checkout/session')->setData('method', $method);
        // ------------------------
        $this->getCheckout()->setStepData('billing', 'allow', true);
        return array();
    }
    public function saveBilling($data, $customerAddressId) {
		
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        //save for event
        $checkoutsission = Mage::getSingleton('checkout/session')->setData('Billingaddress', $data);
        //die('ok');
        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
                ->setEntityType('customer_address')
                ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        if (!empty($customerAddressId)) {

            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                //Crazy code
                $attributeCustomerAddress = $customerAddress->getData();
                $form = Mage::getModel('customer/form');
                $form->setFormCode('customer_register_address');
                $form->setEntityType('customer_address');
                $attributes = $form->getUserAttributes();
                foreach ($attributes as $attribute) {
                    //echo $attribute->getAttributeCode();
                    $address->setData($attribute->getAttributeCode(), $attributeCustomerAddress[$attribute->getAttributeCode()]);
                }            
				try{
					$address->save();
					}catch(Exception $e){
					$e->getMessage();
					}
                //end crazy code
                $addressForm->setEntity($address);
                $addressErrors = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    //return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
            $addressForm->setEntity($address);
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => array_values($addressErrors));
            }
            $addressForm->compactData($addressData);
            //unset billing address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }
            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }
        $address->implodeStreetAddress();
        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int) $data['use_for_shipping'] : 0;

            switch ($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();

                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                                && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                            ->setSameAsBilling(1)
                            ->setSaveInAddressBook(0)
                            ->setShippingMethod($shippingMethod)
                            ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    break;
            }
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        if (!$this->getQuote()->isVirtual() && $this->getCheckout()->getStepData('shipping', 'complete') == true) {
            //Recollect Shipping rates for shipping methods
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        }

        $this->getCheckout()
                ->setStepData('billing', 'allow', true)
                ->setStepData('billing', 'complete', true)
                ->setStepData('shipping', 'allow', true);

        return array();
    }

    public function saveShipping($data, $customerAddressId) {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $checkoutsission = Mage::getSingleton('checkout/session')->setData('Shippingaddress', $data);
        $address = $this->getQuote()->getShippingAddress();

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
                ->setEntityType('customer_address')
                ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                //Crazy code
                $attributeCustomerAddress = $customerAddress->getData();
                $form = Mage::getModel('customer/form');
                $form->setFormCode('customer_register_address');
                $form->setEntityType('customer_address');
                $attributes = $form->getUserAttributes();
                foreach ($attributes as $attribute) {
                    //echo $attribute->getAttributeCode();
                    $address->setData($attribute->getAttributeCode(), $attributeCustomerAddress[$attribute->getAttributeCode()]);
                }
				try{
					$address->save();
					}catch(Exception $e){
					$e->getMessage();
				}
                //end crazy code
                $addressForm->setEntity($address);
                $addressErrors = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
            $addressForm->setEntity($address);
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
            $addressForm->compactData($addressData);
            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }

            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
                ->setStepData('shipping', 'complete', true)
                ->setStepData('shipping_method', 'allow', true);

        return array();
    }

}