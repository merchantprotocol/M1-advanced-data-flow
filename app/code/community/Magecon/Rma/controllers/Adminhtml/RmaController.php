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
class Magecon_Rma_Adminhtml_RmaController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/rma')
                ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
                ->_addBreadcrumb($this->__('RMAs'), $this->__('RMAs'));
        return $this;
    }

    protected function _initRma() {
        $id = $this->getRequest()->getParam('rma_id');
        $rma = Mage::getModel('rma/rma')->load($id);
        if (!$rma->getRmaId() || $rma->getIsDeleted() == 1) {
            $this->_getSession()->addError($this->__('This RMA no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_rma', $rma);
        Mage::register('current_rma', $rma);
        return $rma;
    }

    protected function getRma() {
        return Mage::registry('current_rma');
    }

    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('RMAs'));

        $this->_initAction()
                ->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function editAction() {
        $this->_title($this->__('Sales'))->_title($this->__('RMAs'));

        if ($rma = $this->_initRma()) {
            $this->_initAction();
            $this->_title(sprintf("#%s", $rma->getRmaId()));
            $this->renderLayout();
        }
    }

    public function saveEditAction() {

        if ($rma = $this->_initRma()) {
            try {
                $data = $this->getRequest()->getPost();
                /* $first_name = $this->getRequest()->getPost('first_name');
                  $second_name = $this->getRequest()->getPost('second_name');
                  $company = $this->getRequest()->getPost('company');
                  $telephone = $this->getRequest()->getPost('telephone');
                  $fax = $this->getRequest()->getPost('fax');
                  $street = $this->getRequest()->getPost('street');
                  $city = $this->getRequest()->getPost('city');
                  $country = $this->getRequest()->getPost('country');
                  $region = $this->getRequest()->getPost('region');
                  $post_code = $this->getRequest()->getPost('post_code');
                  $qty = $this->getRequest()->getPost('qty');
                  $condition = $this->getRequest()->getPost('condition');
                  $reason = $this->getRequest()->getPost('reason');
                  $request = $this->getRequest()->getPost('request');
                  $comment = $this->getRequest()->getPost('comment');
                  $action = $this->getRequest()->getPost('action'); */
                for ($i = 0; $i < count($data['qty']); $i++) {
                    if ($data['qty'] > 0)
                        $success = true;
                }

                if (isset($success)) {
                    $model = Mage::getModel('rma/address')->loadByAttribute('rma_id', $rma->getRmaId());
                    $model->setFirstName($data['first_name'])
                            ->setSecondName($data['second_name'])
                            ->setCompany($data['company'])
                            ->setTelephone($data['telephone'])
                            ->setFax($data['fax'])
                            ->setStreet($data['street'])
                            ->setCity($data['city'])
                            ->setCountry($data['country'])
                            ->setRegion($data['region'])
                            ->setPostCode($data['post_code'])
                            ->save();

                    $model = Mage::getModel('rma/products')->getCollection()->addFieldToFilter('rma_id', array('eq' => $rma->getRmaId()))->load();
                    $i = 0;
                    foreach ($model as $item) {
                        $item->setCondition($data['condition'][$i]);
                        $item->setReason($data['reason'][$i]);
                        $item->setRequestType($data['request'][$i]);
                        $item->setComment($data['comment'][$i]);
                        $item->setRmaQty($data['qty'][$i]);
                        $item->setAction($data['action'][$i]);
                        $item->save();

                        if ($data['action'][$i] === 'Return in stock') {
                            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct(Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getProductSku()))->getQty();
                            $new_qty = $qty + (int) $data['qty'][$i];
                            Mage::getModel('cataloginventory/stock_item')->loadByProduct(Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getProductSku()))->setQty($new_qty)->save();
                        }
                        $i++;
                    }

                    $model = Mage::getModel('rma/rma')->load($rma->getRmaId());
                    if (isset($data['rma_id'])) {
                        $model->setRmaId($data['rma_id'])->save();
                    }
                    $model->setShipTo($data['first_name'] . ' ' . $data['second_name'])->setModificationDate(now())->save();
                    $this->_redirect("*/*/index");
                    Mage::getSingleton('core/session')->addSuccess("Your RMA has been editted successfully.");
                } else {
                    $this->_redirect("*/*/index");
                    Mage::getSingleton('core/session')->addError("No data submitted.");
                }
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            }
        }
    }

    public function deleteAction() {

        if ($rma = $this->_initRma()) {
            try {
                $rma->setIsDeleted('1')->save();
                $this->_redirect("*/*/index");
                Mage::getSingleton('core/session')->addSuccess("Your RMA has been deleted successfully.");
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            }
        }
    }

    public function addCommentAction() {
        if ($rma = $this->_initRma()) {
            try {
                $response = false;
                $status = $this->getRequest()->getPost('status');
                $comment = $this->getRequest()->getPost('history_comment');
                $notify = $this->getRequest()->getPost('is_customer_notified');
                $visible = $this->getRequest()->getPost('is_visible_on_front');
                $rmaId = $this->getRequest()->getPost('rma_id');
                $data = array("rma_id" => $rmaId,
                    "comment" => (trim($comment) != '') ? $comment : Mage::getModel('rma/status')->loadByCode($status)->getHistory(),
                    "status" => Mage::getModel('rma/status')->loadByCode($status)->getStatus(),
                    "notify" => $notify,
                    "visible" => $visible,
                    "creation_date" => now());
                $model = Mage::getModel('rma/rma')->load($rma->getRmaId());
                $model->setStatus(Mage::getModel('rma/status')->loadByCode($status)->getStatus());
                $model->setStatusCode($status);
                $model->save();

                $model = Mage::getModel('rma/history');
                $model->setData($data);
                $model->setRmaId($rma->getRmaId());
                $model->save();
                $id = $model->getData('id');

                if (Mage::getModel('rma/rma')->load($rma->getRmaId())->sendUpdateRMAEmail($notify, $id)) {
                    $model->setNotified('1');
                    $model->save();
                }
                Mage::getModel('rma/rma')->load($rma->getRmaId())->sendRMAEmailDepartment();
                $this->loadLayout('empty');
                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Cannot add RMA history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

}