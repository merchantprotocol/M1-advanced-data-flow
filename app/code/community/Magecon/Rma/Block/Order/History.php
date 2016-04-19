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
class Magecon_Rma_Block_Order_History extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('rma/order/completed.phtml');
        $rmas = Mage::getModel('rma/rma')->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())->addAttributeToFilter('is_deleted', array('eq' => 0))->load();

        $this->setRmas($rmas);
        $orders = Mage::getResourceModel('sales/order_collection')
                        ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                        ->addFieldToFilter('state', array('eq' => 'complete'))->addFieldToFilter('is_virtual', array('eq' => '0'))->load();

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My RMAs'));
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();

        $pager_rmas = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
                ->setCollection($this->getRmas());
        $pager_orders = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
                ->setCollection($this->getOrders());
        $this->setChild('pager_rmas', $pager_rmas);
        $this->setChild('pager_orders', $pager_orders);
        return $this;
    }

    public function getCustomer() {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    public function getPagerRmasHtml() {
        return $this->getChildHtml('pager_rmas');
    }

    public function getPagerOrdersHtml() {
        return $this->getChildHtml('pager_orders');
    }

    public function isRequested($order) {
        $collection = Mage::getModel('rma/rma')->getCollection()->load();
        foreach ($collection as $rma) {
            if ($rma->getRealOrderId() == $order->getRealOrderId() && $rma->getIsDeleted() == 0) {
                $requested = true;
            }
        }
        if (isset($requested)) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    public function getViewRmaUrl($rma) {
        return $this->getUrl('*/*/view', array('rma_id' => $rma->getRmaId()));
    }

    public function getCancelUrl($rma) {
        return $this->getUrl('*/*/cancel', array('rma_id' => $rma->getRmaId()));
    }

    public function getBackUrl() {
        return $this->getUrl('customer/account/');
    }

    public function getRequestUrl($order) {
        return $this->getUrl('*/*/request', array('order_id' => $order->getId()));
    }

}