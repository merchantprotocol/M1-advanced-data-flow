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
class Magecon_Rma_OrderController extends Mage_Core_Controller_Front_Action {

    public function preDispatch() {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function completedAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My RMAs'));
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function viewAction() {
        if (!$this->_loadValidRma()) {
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('rma/order/completed');
        }
        $this->renderLayout();
    }

    public function cancelAction() {

        $this->loadLayout();
        if (!$this->_loadValidRmaCancel()) {
            return;
        }
        try {
            $rma = Mage::registry('current_rma');
            $rma->setStatusCode(Mage::getModel('rma/status')->loadByCode('canceled_by_customer')->getCode());
            $rma->setStatus(Mage::getModel('rma/status')->loadByCode('canceled_by_customer')->getStatus());
            $rma->save();
            $model = Mage::getModel('rma/history');
            $model->setHistoryCommmentForCanceledStatus($rma->getRmaId());
            $model->save();
            $id = $model->getData('id');
            if ($rma->sendUpdateRMAEmail(1, $id)) {
                $model = Mage::getModel('rma/history')->load($id);
                $model->setNotified('1');
                $model->save();
            }
            $rma->sendRMAEmailDepartment();
            Mage::getSingleton('core/session')->addSuccess("Your RMA was canceled.");
            $this->_redirect("rma/order/completed");
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

    public function requestAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Items available for RMA'));
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = Mage::registry('current_order');
        $items = $order->getItemsCollection();
        $this->renderLayout();
    }

    protected function _loadValidOrder($orderId = null) {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        $model = Mage::getModel('rma/rma')->getCollection();
        foreach ($model as $rma) {
            if ($rma->getOrderId() == $order->getId() && $rma->getIsDeleted() == 0) {
                Mage::getSingleton('core/session')->addError("This order is not available for RMA.");
                $this->_redirect('*/*/completed');
                return;
            }
        }
        if (!$order->getId() || $order->getCustomerId() != $this->getCustomerId()) {
            Mage::getSingleton('core/session')->addError("This order does not exist.");
            $this->_redirect('*/*/completed');
            return;
        }
        Mage::register('current_order', $order);
        return true;
    }

    protected function _loadValidRma($rmaId = null) {
        if (null === $rmaId) {
            $rmaId = (int) $this->getRequest()->getParam('rma_id');
        }
        if (!$rmaId) {
            $this->_forward('noRoute');
            return false;
        }
        $rma = Mage::getModel('rma/rma')->load($rmaId);
        if (!$rma->getRmaId() || $rma->getCustomerId() != $this->getCustomerId() || $rma->getIsDeleted() == 1) {
            Mage::getSingleton('core/session')->addError("This RMA does not exist.");
            $this->_redirect('*/*/completed');
            return;
        }
        Mage::register('current_rma', $rma);
        return true;
    }

    protected function _loadValidRmaCancel($rmaId = null) {
        if (null === $rmaId) {
            $rmaId = (int) $this->getRequest()->getParam('rma_id');
        }
        if (!$rmaId) {
            $this->_forward('noRoute');
            return false;
        }
        $rma = Mage::getModel('rma/rma')->load($rmaId);
        if (!$rma->getRmaId() || $rma->getCustomerId() != $this->getCustomerId() || $rma->getIsDeleted() == 1 || $rma->getStatusCode() != 'pending') {
            Mage::getSingleton('core/session')->addError("This RMA does not exist.");
            $this->_redirect('*/*/completed');
            return;
        }
        Mage::register('current_rma', $rma);
        return true;
    }

    public function submitAction() {
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $order_id = $data['order_id'];
                foreach ($data['qty'] as $q) {
                    if ($q > 0)
                        $success = true;
                }
                if (isset($success)) {
                    if ($data['shipping_address_id'] != null) {
                        $address = Mage::getModel('customer/address')->load($data['shipping_address_id']);
                    } else {
                        $order = Mage::getModel('sales/order')->load($data['order_id']);
                        $address = $order->getShippingAddress();
                    }
                    $model = Mage::getModel('rma/rma');
                    $model->setData($data)
                            ->setShipTo($address->getFirstname() . " " . $address->getLastname())
                            ->setStatus(Mage::getModel('rma/status')->loadByCode('pending')->getStatus())
                            ->setStatusCode(Mage::getModel('rma/status')->loadByCode('pending')->getCode())
                            ->setCreationDate(now())->setModificationDate(now());
                    $model->save();

                    $rmaId = $model->getData('rma_id');
                    $model = Mage::getModel('rma/history');
                    $model->setHistoryCommmentForPendingStatus($rmaId);
                    $model->save();
                    $id = $model->getData('id');

                    $addressData = array("rma_id" => $rmaId,
                        "first_name" => $address->getFirstname(),
                        "second_name" => $address->getLastname(),
                        "company" => $address->getCompany(),
                        "telephone" => $address->getTelephone(),
                        "fax" => $address->getFax(),
                        "street" => $address->getStreetFull(),
                        "city" => $address->getCity(),
                        "country" => $address->getCountryId(),
                        "region" => $address->getRegion(),
                        "post_code" => $address->getPostcode());
                    $model = Mage::getModel('rma/address');
                    $model->setData($addressData);
                    $model->save();

                    for ($i = 0; $i < count($data['qty']); $i++) {
                        if ($data['qty'][$i] > 0) {
                            $itemData = array("rma_id" => $rmaId,
                                "comment" => isset($data['comment'][$i]) ? $data['comment'][$i] : "",
                                "reason" => isset($data['reason'][$i]) ? $data['reason'][$i] : "",
                                "condition" => isset($data['condition'][$i]) ? $data['condition'][$i] : "",
                                "request_type" => isset($data['request'][$i]) ? $data['request'][$i] : "",
                                "product_sku" => $data['sku'][$i],
                                "product_name" => $data['name'][$i],
                                "rma_qty" => $data['qty'][$i]);
                            $model = Mage::getModel('rma/products');
                            $model->setData($itemData);
                            $model->save();
                        }
                    }

                    if (Mage::getModel('rma/rma')->load($rmaId)->sendNewRMAEmail($id)) {
                        $model = Mage::getModel('rma/history')->load($id);
                        $model->setNotified('1');
                        $model->save();
                    }
                    Mage::getModel('rma/rma')->load($rmaId)->sendRMAEmailDepartment();
                    Mage::getSingleton('core/session')->addSuccess("Your RMA request was successful.");
                    $this->_redirect("rma/order/completed");
                } else {
                    $this->_redirect("rma/order/requestRma/order_id/{$order_id}");
                    throw new Exception('No data submited');
                    return;
                }
            } else {
                $this->_redirect("rma/order/completed");
                throw new Exception('No data submited');
                return;
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

    public function getCustomerId() {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    public function getCustomerName() {
        return Mage::getSingleton('customer/session')->getCustomer()->getName();
    }

    public function printAction() {
        if (!$this->_loadValidRma()) {
            return;
        }
        $this->loadLayout('print');
        $this->renderLayout();
    }

}