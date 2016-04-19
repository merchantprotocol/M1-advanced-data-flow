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
class Magecon_Rma_Model_Rma extends Mage_Core_Model_Abstract {

    const XML_PATH_EMAIL_TEMPLATE = 'rma/email/template_rma';
    const XML_PATH_EMAIL_ENABLED = 'rma/email/enabled_rma';
    const XML_PATH_EMAIL_IDENTITY = 'rma/email/identity_rma';
    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'rma/email/template_rma_update';
    const XML_PATH_UPDATE_EMAIL_ENABLED = 'rma/email/enabled_rma_update';
    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'rma/email/identity_rma_update';
    const XML_PATH_EMAIL_TEMPLATE_DEPARTMENT = 'rma/email/template_rma_department';
    const XML_PATH_EMAIL_ENABLED_DEPARTMENT = 'rma/email/enabled_rma_department';
    const XML_PATH_EMAIL_IDENTITY_DEPARTMENT = 'rma/email/identity_rma_department';
    const XML_PATH_EMAIL_DEPARTMENT = 'rma/email/rma_email';

    public function _construct() {
        parent::_construct();
        $this->_init('rma/rma');
    }

    public function sendNewRMAEmail($id) {
        if (Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED)) {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $storeId = $this->getStore()->getId();
            if (!Mage::helper('rma')->canSendNewRMAEmail($storeId)) {
                return $this;
            }
            $email = Mage::getModel('core/email_template');

            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
            $destEmail = Mage::getModel('customer/customer')->load($this->getCustomerId())->getEmail();
            $variables = array(
                'request' => $this,
                'comment' => Mage::getModel('rma/history')->load($id)->getComment()
            );
            $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
            $email->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                            $template, $sender, $destEmail, $customerName, $variables
            );
            $translate->setTranslateInline(true);
            return $email->getSentSuccess();
        } else {
            return false;
        }
    }

    public function sendUpdateRMAEmail($notify, $id) {
        if (Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_ENABLED)) {
            if ($notify) {
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);
                $storeId = $this->getStore()->getId();
                if (!Mage::helper('rma')->canSendNewRMAEmail($storeId)) {
                    return $this;
                }
                $email = Mage::getModel('core/email_template');

                $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
                $customerName = $this->getCustomerName();
                $destEmail = Mage::getModel('customer/customer')->load($this->getCustomerId())->getEmail();
                $variables = array(
                    'request' => $this,
                    'comment' => Mage::getModel('rma/history')->load($id)->getComment()
                );
                $sender = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY);
                $email->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                        ->sendTransactional(
                                $template, $sender, $destEmail, $customerName, $variables
                );
                $translate->setTranslateInline(true);
                return $email->getSentSuccess();
            }
        } else {
            return false;
        }
    }

    public function sendRMAEmailDepartment() {
        if (Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED_DEPARTMENT) && trim(Mage::getStoreConfig(self::XML_PATH_EMAIL_DEPARTMENT)) != '') {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $storeId = $this->getStore()->getId();
            $email = Mage::getModel('core/email_template');
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_DEPARTMENT);
            $destEmail = Mage::getStoreConfig(self::XML_PATH_EMAIL_DEPARTMENT);
            $variables = array(
                'request' => $this
            );
            $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY_DEPARTMENT);
            $email->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                            $template, $sender, $destEmail, 'name', $variables
            );
            $translate->setTranslateInline(true);
            return $email->getSentSuccess();
        } else {
            return false;
        }
    }

    public function getStore() {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }

    public function getId() {
        return $this->getRmaId();
    }

    public function getRMAOrderId() {
        return $this->getRealOrderId();
    }

    public function getRMACreationDate($format) {
        return Mage::helper('core')->formatDate($this->getCreationDate(), $format, true);
    }

    public function getOrderCreationDate($format) {
        return Mage::helper('core')->formatDate($this->getScanDate(), $format, true);
    }

    public function getAddress() {
        $addresses = Mage::getModel('rma/address')->getCollection();
        foreach ($addresses as $address) {
            if ($address->getRmaId() == $this->getRmaId())
                return $address;
        }
    }

    public function getCustomerShippingAddress() {

        $address = $this->getAddress();

        if ($address) {

            if (strlen($address->getCompany()) > 0) {
                $company = $address->getCompany() . '<br />';
            } else {
                $company = '';
            }
            if (strlen($address->getRegion()) > 0) {
                $region = ', ' . $address->getRegion();
            } else {
                $region = '';
            }
            if (strlen($address->getFax()) > 0) {
                $fax = '<br />' . 'F: ' . $address->getFax();
            } else {
                $fax = '';
            }
            return $address->getFirstName() . ' ' . $address->getSecondName() . '<br />' .
                    $company . $address->getStreet() . '<br />' .
                    $address->getCity() . $region . ', ' . $address->getPostCode() . '<br />' .
                    Mage::getModel('directory/country')->loadByCode($address->getCountry())->getName() . '<br />' .
                    'T: ' . $address->getTelephone() . $fax;
        }
    }

    public function getMerchantShippingAddress() {

        if (strlen(Mage::getStoreConfig('rma/address_settings/company')) > 0) {
            $company = Mage::getStoreConfig('rma/address_settings/company') . '<br />';
        } else {
            $company = '';
        }
        if (strlen(Mage::getStoreConfig('rma/address_settings/state')) > 0) {
            $state = ', ' . Mage::getStoreConfig('rma/address_settings/state');
        } else {
            $state = '';
        }
        if (strlen(Mage::getStoreConfig('rma/address_settings/fax')) > 0) {
            $fax = '<br />' . 'F: ' . Mage::getStoreConfig('rma/address_settings/fax');
        } else {
            $fax = '';
        }

        if (strlen(Mage::getStoreConfig('rma/address_settings/post_code')) > 0) {
            $zip = ', ' . Mage::getStoreConfig('rma/address_settings/post_code');
        } else {
            $zip = '';
        }

        if (strlen(Mage::getStoreConfig('rma/address_settings/telephone')) > 0) {
            $telephone = '<br />' . 'T: ' . Mage::getStoreConfig('rma/address_settings/telephone');
        } else {
            $telephone = '';
        }
        return $company . Mage::getStoreConfig('rma/address_settings/first_name') . ' ' . Mage::getStoreConfig('rma/address_settings/second_name') . '<br />' .
                Mage::getStoreConfig('rma/address_settings/street') . '<br />' .
                Mage::getStoreConfig('rma/address_settings/city') . $state . $zip . '<br />' .
                Mage::getModel('directory/country')->loadByCode(Mage::getStoreConfig('rma/address_settings/country'))->getName() . $telephone . $fax;
    }

    public function getShippingMethod() {
        return Mage::getStoreConfig('rma/settings/shipping_method');
    }

    public function getShippingInformation() {
        return Mage::getStoreConfig('rma/settings/shipping_information');
    }

    public function getRMAStatus() {
        return $this->getStatus();
    }

    public function getRMACustomerName() {
        return $this->getCustomerName();
    }

    public function getStatusEmailMsg() {
        return Mage::getModel('rma/status')->loadByCode($this->getStatusCode())->getEmail();
    }

    public function getStatusEmailAdminMsg() {
        return Mage::getModel('rma/status')->loadByCode($this->getStatusCode())->getAdminEmail();
    }

    public function getEmail() {
        return Mage::getModel('customer/customer')->load($this->getCustomerId())->getEmail();
    }

    public function getItems() {
        return Mage::getModel('rma/products')->getCollection()->addAttributeToFilter('rma_id', array('eq' => $this->getRmaId()))->load();
    }

    public function getVisibleStatusHistory() {
        return Mage::getModel('rma/history')->getCollection()->addAttributeToFilter('rma_id', array('eq' => $this->getRmaId()))->addAttributeToFilter('visible', array('eq' => '1'))->load();
    }

    public function setPendingStatus() {
        $status = Mage::getModel('rma/status')->loadByCode('pending');
        $this->setStatusCode($status->getCode());
        $this->setStatus($status->getStatus());
    }

}