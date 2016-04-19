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
class Magecon_Rma_Block_Rma_Print extends Mage_Sales_Block_Items_Abstract {

    protected function _prepareLayout() {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Print RMA # %s', $this->getRma()->getRmaId()));
        }
    }

    public function getRma() {
        return Mage::registry('current_rma');
    }

    protected function _prepareItem(Mage_Core_Block_Abstract $renderer) {
        $renderer->setPrintStatus(true);

        return parent::_prepareItem($renderer);
    }

    public function getCustomerAddress() {
        $model = Mage::getModel('rma/address')->getCollection();
        foreach ($model as $address) {
            if ($address->getRmaId() == $this->getRma()->getRmaId()) {
                $customer_address = $address;
                break;
            }
        }
        $company = ($customer_address->getCompany() != '') ? '' . $customer_address->getCompany() . ', ' : '';
        $state = ($customer_address->getRegion() != '') ? ', ' . $customer_address->getRegion() : '';
        $zip = ($customer_address->getPostCode() != '') ? ', ' . $customer_address->getPostCode() : '';
        $fax = ($customer_address->getFax() != '') ? '<br />F: ' . $customer_address->getFax() : '';

        return $company . $customer_address->getFirstName() . " " . $customer_address->getSecondName() . "<br />" . $customer_address->getStreet() . "<br />" .
                $customer_address->getCity() . $state . $zip . '<br />' .
                Mage::getModel('directory/country')->loadByCode($customer_address->getCountry())->getName() . '<br />' .
                'T: ' . $customer_address->getTelephone() . $fax;
    }

}