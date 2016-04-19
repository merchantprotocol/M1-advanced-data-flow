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
 * Customerattribute Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Model_Observer {
    public function customer_save_after($obs){
        
        $customer = $obs->getCustomer();
        if ($customer->getDefaultBilling()) return false;
        
        if (!$customer->getData('school_address1') 
           && !$customer->getData('school_postal_code')) return;
        
        $regionModel = Mage::getModel('directory/region')
                ->loadByCode(Mage::helper('idpas400')->convert_state(Mage::helper('idpas400')->getAttributeOptionText(
                        $customer->getData('school_state'),'school_state')), 'US');
        
        $data = array(
            'firstname' => $customer->getData('first_name')
                ? $customer->getData('first_name')
                : $customer->getData('firstname'),
            'lastname' => $customer->getData('last_name')
                ? $customer->getData('last_name')
                : $customer->getData('lastname'),
            'street' => array (
                '0' => $customer->getData('school_address1'),
                '1' => $customer->getData('school_address2'),
            ),
            'city' => $customer->getData('school_city'),
            'region_id' => $regionModel->getId(),
            'region' => '',
            'postcode' => $customer->getData('school_postal_code'),
            'country_id' => 'US', /* Croatia */
            'telephone' => $customer->getData('school_telephone'),
        );
        
        if (!trim($data['firstname']) &&strpos($customer->getData('name'), ' ')!==false) {
            @list($firstname, $lastname) = explode(' ',$customer->getData('name'));
            $data['firstname'] = $firstname;
            $data['lastname'] = $lastname;
        }
        $data['first_name'] = $data['firstname'];
        $data['last_name'] = $data['lastname'];
        
        $address = Mage::getModel('customer/address');
        $address->setData($data)
            ->setCustomerId($customer->getId())
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
        $address->save();
    }
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Customerattribute_Model_Observer
     */

    /**
      Creat by ThinhND
     */
    public function saveAttribute($observer) {
        $redirectBack = Mage::app()->getRequest()->getParam('back', false);
        $customerId = Mage::app()->getRequest()->getParam('customer_id');
        if ($customerId) {
            $data = Mage::app()->getRequest()->getParam('tabCustomerattribute');

            /* validate */
            $customer = Mage::registry('current_customer');
            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                    ->setFormCode('tabCustomerattribute')
                    ->ignoreInvisible(false)
            ;
            $data = Mage::helper('customerattribute')->getAttributeLabel($data);
            $formData = $data;
            foreach ($_FILES["tabCustomerattribute"]["name"] as $labelAttribute => $flie) {
                $formData[$labelAttribute]["name"] = $flie;
                $formData[$labelAttribute]["type"] = $_FILES["tabCustomerattribute"]["type"][$labelAttribute];
                $formData[$labelAttribute]["tmp_name"] = $_FILES["tabCustomerattribute"]["tmp_name"][$labelAttribute];
                $formData[$labelAttribute]["error"] = $_FILES["tabCustomerattribute"]["error"][$labelAttribute];
                $formData[$labelAttribute]["size"] = $_FILES["tabCustomerattribute"]["size"][$labelAttribute];
            }
            $errors = $customerForm->validateData($formData);
            if ($errors !== true) {
                Mage::getSingleton('adminhtml/session')->unsMessages();
                foreach ($errors as $error) {
                    Mage::getSingleton('adminhtml/session')->addError('Customer Attribute error: ' . $error);
                }
                Mage::getSingleton('adminhtml/session')->setCustomerData($data);

                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('*/customer/edit', array('id' => $customer->getId())));
                Mage::app()->getResponse()->sendResponse();
                exit;


                return;
            }
            /* end validate */
            if (isset($_FILES['tabCustomerattribute']['name']) && $_FILES['tabCustomerattribute']['name'] != '') {


                foreach ($_FILES['tabCustomerattribute']['name'] as $filed => $file) {
                    try {
                        if ($data[$filed]['delete']) {

                            $data[$filed] = null;
                        } else {
                            $path = Mage::helper('customerattribute/image')->createImageFolder($file);
                            $uploader = new Varien_File_Uploader('tabCustomerattribute[' . $filed . ']');
                            //$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            $result = $uploader->save($path, $file);
                            $data[$filed] = Mage::helper('customerattribute/image')->getUrlkey($file); //$file;
                        }
                    } catch (Exception $e) {
                        
                    }
                }
            }
            if (isset($_FILES)) {


                foreach ($_FILES as $filed => $file) {

                    if ($filed != 'tabCustomerattribute' && $filed != 'address') {
                        try {
                            if ($data[$filed]['delete']) {

                                $data[$filed] = null;
                            } else {
                                $pathImage = Mage::helper('customerattribute/image')->createImageFolder($file['name']); //zend_debug::dump($pathImage);die();
                                $uploader = new Varien_File_Uploader($filed); //die($filed);
                                // $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                                $uploader->setAllowRenameFiles(false);
                                $uploader->setFilesDispersion(false);
                                $result = $uploader->save($pathImage, $file['name']); //die($filed);
                                $data[$filed] = Mage::helper('customerattribute/image')->getUrlkey($file['name']); //echo $filed;
                            }
                        } catch (Exception $e) {
                            
                        }
                    }
                }
            }
            try {
                $customer = Mage::getModel('customer/customer');
                $customer->load($customerId)->addData($data);
                $customer->setId($customerId)->save();
                
                
                if ($redirectBack == null) {
                    Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('customerattributeadmin/adminhtml_managecustomer'));
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addErrorr(
                        Mage::helper('customerattribute')->__('An error occurred while saving the fffffffcustomer'));
                if ($redirectBack == null) {
                    Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('customerattributeadmin/managecustomer/index'));
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }

                return;
            }
        }
    }

    /* End */

    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function blockToHtmlBefore(Varien_Event_Observer $observer) {

        $event = $observer->getEvent();
        $block = $event->getBlock();
        if (get_class($block) == 'Mage_Customer_Block_Form_Register') {
            $block->setTemplate('customerattribute/customer/form/register.phtml');
            $childrenblock = new Magestore_Customerattribute_Block_Customer_Form();
            $childrenblock->setFormCode('customer_account_create');
            $childrenblock->setEntityModelClass('customer/customer');
            $childrenblock->setParentBlock($block);
            $childrenblock->loadTemplate();
            $childrenblock->setTemplate('customerattribute/customer/form/userattributes.phtml');
            $block->setChild('customer_form_user_attributes', $childrenblock);
        } elseif (get_class($block) == 'Mage_Customer_Block_Form_Edit') {
            $block->setTemplate('customerattribute/customer/form/edit.phtml');
        }
    }

    public function salesOrderSaveBefore(Varien_Event_Observer $observer) {

        $customerAddressBilling = $observer->getEvent()->getOrder()->getBillingAddress()->getCustomerAddress();
        if ($observer->getEvent()->getOrder()->getShippingAddress())
            $customerAddressShipping = $observer->getEvent()->getOrder()->getShippingAddress()->getCustomerAddress();
        if ($customerAddressBilling) {

            $data = Mage::getSingleton('checkout/session')->getData();
            $billingAddress = $data['Billingaddress'];

            $form = Mage::getModel('customer/form');
            $form->setFormCode('customer_register_address');
            $form->setEntityType('customer_address');
            $attributes = $form->getUserAttributes();
            foreach ($attributes as $attribute) {
                //echo $attribute->getAttributeCode();
                $customerAddressBilling->setData($attribute->getAttributeCode(), $billingAddress[$attribute->getAttributeCode()]);
            }
            try {
                $customerAddressBilling->save();
            } catch (Exception $e) {
                $e->getMessage();
            }
        }
        if ($customerAddressShipping) {

            $data = Mage::getSingleton('checkout/session')->getData();
            $shippingAddress = $data['Shippingaddress'];

            $form = Mage::getModel('customer/form');
            $form->setFormCode('customer_register_address');
            $form->setEntityType('customer_address');
            $attributes = $form->getUserAttributes();
            foreach ($attributes as $attribute) {
                $customerAddressShipping->setData($attribute->getAttributeCode(), $shippingAddress[$attribute->getAttributeCode()]);
            }
            try {
                $customerAddressShipping->save();
            } catch (Exception $e) {
                $e->getMessage();
            }
        }
    }

    public function salesOrderSaveAfter(Varien_Event_Observer $observer) {

        $customer = $observer->getEvent()->getOrder()->getCustomer();
        if ($customer) {
            $order = array();
            $order['order_id'] = $observer->getEvent()->getOrder()->getId();
            $order_attribute = Mage::getModel('customerattribute/orderattribute');
            $data = Mage::getSingleton('checkout/session')->getData();
            $method = $data['method'];
            $arrayData = $data['Billingaddress'];
            $form = Mage::getModel('customer/form');
            $form->setFormCode('checkout_register');
            $form->setEntity(Mage::getModel('customer/customer'));
            $attributes = $form->getUserAttributes();
            foreach ($attributes as $attribute) {
                $customer->setData($attribute->getAttributeCode(), $arrayData[$attribute->getAttributeCode()]);
            }
            try {
                if ($method == 'guest') {
                    $customer_attributes = Mage::getModel('customer/attribute')->getCollection();
                    foreach ($customer_attributes as $customer_attribute) {
                        $code = 'customer_' . $customer_attribute->getAttributeCode();
                        if (!is_array($arrayData[$customer_attribute->getAttributeCode()]))
                            $order[$code] = $arrayData[$customer_attribute->getAttributeCode()];
                        else
                            $order[$code] = implode(',', $arrayData[$customer_attribute->getAttributeCode()]);
                    }
                    $ordered = Mage::getModel('customerattribute/orderattribute')->getCollection()
                                    ->addFieldToFilter('order_id', $order['order_id'])->getFirstItem();
                    if ($ordered->getId())
                        $order_attribute->setData($order)->setId($ordered->getId())->save();
                    else
                        $order_attribute->setData($order)->save();
                }elseif ($method == 'register') {
                    $customer->save();
                    $order_data = $customer->getData();
                    $customer_attributes = Mage::getModel('customer/attribute')->getCollection();
                    foreach ($customer_attributes as $customer_attribute) {
                        $code = 'customer_' . $customer_attribute->getAttributeCode();
                        $order[$code] = $order_data[$customer_attribute->getAttributeCode()];
                    }
                    $ordered = Mage::getModel('customerattribute/orderattribute')->getCollection()
                                    ->addFieldToFilter('order_id', $order['order_id'])->getFirstItem();
                    if ($ordered->getId())
                        $order_attribute->setData($order)->setId($ordered->getId())->save();
                    else
                        $order_attribute->setData($order)->save();
                }else {
                    $customer->save();
                    $customerId = $customer->getId();
                    $order_data = Mage::getModel('customer/customer')->load($customerId)->getData();
                    $customer_attributes = Mage::getModel('customer/attribute')->getCollection();
                    foreach ($customer_attributes as $customer_attribute) {
                        $code = 'customer_' . $customer_attribute->getAttributeCode();
                        $order[$code] = $order_data[$customer_attribute->getAttributeCode()];
                    }
                    $ordered = Mage::getModel('customerattribute/orderattribute')->getCollection()
                                    ->addFieldToFilter('order_id', $order['order_id'])->getFirstItem();
                    if ($ordered->getId())
                        $order_attribute->setData($order)->setId($ordered->getId())->save();
                    else
                        $order_attribute->setData($order)->save();
                }
            } catch (Exception $e) {
                $e->getMessage();
            }
        }
    }

}
