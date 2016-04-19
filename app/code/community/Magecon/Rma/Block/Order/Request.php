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
class Magecon_Rma_Block_Order_Request extends Mage_Core_Block_Template {

    protected $_address = null;

    public function __construct() {
        parent::__construct();
        $this->setTemplate('rma/order/request.phtml');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

    public function getItemHtml(Mage_Sales_Model_Order_Item $item) {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }

    public function getItems() {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel("sales/order")->load($id);
        return $order->getAllItems();
    }

    public function getCustomer() {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    public function isCustomerLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function customerHasAddresses() {
        return count($this->getCustomer()->getAddresses());
    }

    public function getAddressesHtmlSelect($type) {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $select = $this->getLayout()->createBlock('core/html_select')
                    ->setName($type . '_address_id')
                    ->setId($type . '-address-select')
                    ->setClass('address-select')
                    ->setOptions($options);


            return $select->getHtml();
        }
        return '';
    }

    public function getAddress() {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = Mage::getModel('sales/quote_address');
            }
        }

        return $this->_address;
    }

    public function hasError() {
        return $this->getQuote()->getHasError();
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($rma) {
        return $this->getUrl('*/*/view', array('rma_id' => $rma->getRmaId()));
    }

    public function getSubmitUrl($order) {
        return $this->getUrl('*/*/submit', array('order_id' => $order->getId()));
    }

    public function getOrder() {
        return Mage::registry('current_order');
    }

    public function getRma() {
        $model = Mage::getModel('rma/rma')->load();
        return $model;
    }

}