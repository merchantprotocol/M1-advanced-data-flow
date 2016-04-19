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
class Magecon_Rma_Adminhtml_Rma_CreateController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/rma')
                ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
                ->_addBreadcrumb($this->__('RMAs'), $this->__('RMAs'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('RMAs'));
        $this->_title(sprintf("Create RMA"));
        $this->_initAction()
                ->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function newAction() {

        $order_id = $this->getRequest()->getParam('order_id');
        $model = Mage::getModel('rma/rma')->getCollection();
        foreach ($model as $rma) {
            if ($rma->getOrderId() == $order_id && $rma->getIsDeleted() == 0) {
                $requested = true;
            }
        }
        if (Mage::getModel('sales/order')->load($order_id)->getIsVirtual()) {
            $this->_redirect("*/adminhtml_rma_create");
            Mage::getSingleton('core/session')->addError("The order is virtual!");
            return;
        }
        $this->_title(sprintf("Create RMA - Order #%s", Mage::getModel('sales/order')->load($order_id)->getRealOrderId()));
        $this->loadLayout();
        $this->renderLayout();
        if (isset($requested)) {
            $this->_redirect("*/adminhtml_rma_create");
            Mage::getSingleton('core/session')->addError("A RMA from this order already exists");
            return;
        }
    }

    public function saveAction() {
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $order_id = $this->getRequest()->getParam('order_id');
                $customer_id = Mage::getModel('sales/order')->load($order_id)->getCustomerId();
                foreach ($data['qty'] as $q) {
                    if ($q > 0)
                        $success = true;
                }

                if (isset($success)) {
                    if (isset($data['address_id'])) {
                        $address = Mage::getModel('customer/address')->load($data['address_id']);
                    }
                    $rmaData = array("order_id" => $order_id,
                        "real_order_id" => Mage::getModel('sales/order')->load($order_id)->getRealOrderId(),
                        "scan_date" => Mage::getModel('sales/order')->load($order_id)->getCreatedAt(),
                        "customer_id" => $customer_id,
                        "customer_name" => Mage::getModel('customer/customer')->load($customer_id)->getName(),
                        "ship_to" => isset($data['address_id']) ? '' . $address->getFirstname() . ' ' . $address->getLastname() . '' : '' . $data['first_name'] . ' ' . $data['second_name'] . '',
                        "creation_date" => now(),
                        "modification_date" => now());
                    $model = Mage::getModel('rma/rma');
                    $model->setData($rmaData);
                    $model->setStatus(Mage::getModel('rma/status')->loadByCode('pending')->getStatus())
                            ->setStatusCode(Mage::getModel('rma/status')->loadByCode('pending')->getCode());
                    $model->save();
                    $rmaId = $model->getData('rma_id');
                    $model = Mage::getModel('rma/history');
                    $model->setHistoryCommmentForPendingStatus($rmaId);
                    $model->save();
                    $id = $model->getData('id');
                    $addressData = array("rma_id" => $rmaId,
                        "first_name" => isset($address) ? $address->getFirstname() : $data['first_name'],
                        "second_name" => isset($address) ? $address->getLastname() : $data['second_name'],
                        "company" => isset($address) ? $address->getCompany() : $data['company'],
                        "telephone" => isset($address) ? $address->getTelephone() : $data['telephone'],
                        "fax" => isset($address) ? $address->getFax() : $data['fax'],
                        "street" => isset($address) ? $address->getStreetFull() : $data['street'],
                        "city" => isset($address) ? $address->getCity() : $data['city'],
                        "country" => isset($address) ? $address->getCountryId() : $data['country'],
                        "region" => isset($address) ? $address->getRegion() : $data['region'],
                        "post_code" => isset($address) ? $address->getPostcode() : $data['post_code']);
                    $model = Mage::getModel('rma/address');
                    $model->setData($addressData);
                    $model->save();
                    $i = 0;

                    foreach (Mage::getModel('sales/order')->load($order_id)->getAllItems() as $item) {
                        if ($data['qty'][$i] > 0 && $data['sku'][$i] == $item->getSku()) {
                            $itemData = array("rma_id" => $rmaId,
                                "comment" => isset($data['comment'][$i]) ? $data['comment'][$i] : "",
                                "reason" => isset($data['reason'][$i]) ? $data['reason'][$i] : "",
                                "condition" => isset($data['condition'][$i]) ? $data['condition'][$i] : "",
                                "request_type" => isset($data['request'][$i]) ? $data['request'][$i] : "",
                                "product_sku" => $item->getSku(),
                                "product_name" => $item->getName(),
                                "rma_qty" => $data['qty'][$i]);
                            $model = Mage::getModel('rma/products');
                            $model->setData($itemData);
                            $model->save();
                        }

                        $i++;
                    }
                    if (Mage::getModel('rma/rma')->load($rmaId)->sendNewRMAEmail($id)) {
                        $model = Mage::getModel('rma/history')->load($id);
                        $model->setNotified('1');
                        $model->save();
                    }
                    Mage::getModel('rma/rma')->load($rmaId)->sendRMAEmailDepartment();
                    $this->_redirect("*/adminhtml_rma/index");
                    Mage::getSingleton('core/session')->addSuccess("Your RMA request was successful.");
                } else {
                    $this->_redirect("*/adminhtml_rma_create/new/order_id/{$order_id}");
                    throw new Exception('No data submited');
                }
            } else {
                throw new Exception('No data submited');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

}