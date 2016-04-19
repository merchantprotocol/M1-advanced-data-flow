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
class Magecon_Rma_Adminhtml_Rma_StatusController extends Mage_Adminhtml_Controller_Action {

    protected function _construct() {
        $this->setUsedModuleName('Magecon_Rma');
    }

    protected function _initStatus() {
        $statusId = $this->getRequest()->getParam('status_id');
        if ($statusId) {
            $status = Mage::getModel('rma/status')->load($statusId);
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * Statuses grid page
     */
    public function indexAction() {
        $this->_title($this->__('RMA'))->_title($this->__('RMA Statuses'));
        $this->loadLayout()
                ->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * New status form
     */
    public function newAction() {
        $data = $this->_getSession()->getFormData(true);
        if ($data) {
            $status = Mage::getModel('rma/status')
                    ->setData($data);
            Mage::register('current_status', $status);
        }
        $this->_title($this->__('RMA'))->_title($this->__('Create New RMA Status'));
        $this->loadLayout()
                ->renderLayout();
    }

    /**
     * Editing existing status form
     */
    public function editAction() {
        $status = $this->_initStatus();
        if ($status) {
            Mage::register('current_status', $status);
            $this->_title($this->__('RMA'))->_title($this->__('Edit RMA Status'));
            $this->loadLayout()
                    ->renderLayout();
        } else {
            $this->_getSession()->addError(
                    Mage::helper('sales')->__('Order status does not exist.')
            );
            $this->_redirect('*/');
        }
    }

    /**
     * Save status form processing
     */
    public function saveAction() {
        $data = $this->getRequest()->getPost();
        $isNew = $this->getRequest()->getParam('is_new');
        if ($data) {

            $statusId = $this->getRequest()->getParam('status_id');

            $helper = Mage::helper('rma');
            $data['status'] = $helper->stripTags($data['status']);

            $status = Mage::getModel('rma/status')
                    ->load($statusId);
            if ($isNew && $status->getCode()) {
                $statusCode = $data['code'] = $helper->stripTags($data['code']);
                $this->_getSession()->addError(
                        Mage::helper('rma')->__('Order status with the same status code already exist.')
                );
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/new');
                return;
            }
            if ($isNew) {
                $status->setData($data);
            } else {
                $status->setStatus($data['status']);
                $status->setPosition($data['position']);
                $status->setEmail($data['email']);
                $status->setHistory($data['history']);
                $status->setAdminEmail($data['admin_email']);
                $model = Mage::getModel('rma/rma')->getCollection()->addAttributeToFilter('status_code', array('eq' => $data['code']));
                foreach ($model as $rma) {
                    $rma->setStatus($data['status'])->save();
                }
            }
            try {
                $status->save();
                $this->_getSession()->addSuccess(Mage::helper('rma')->__('The order status has been saved.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                        $e, Mage::helper('rma')->__('An error occurred while saving order status. The status has not been added.')
                );
            }
            $this->_getSession()->setFormData($data);
            if ($isNew) {
                $this->_redirect('*/*/new');
            } else {
                $this->_redirect('*/*/edit', array('status_id' => $this->getRequest()->getParam('status_id')));
            }
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        $statusId = $this->getRequest()->getParam('status_id');
        $model = Mage::getModel('rma/status')->load($statusId);
        try {
            $model->delete();
            $this->_getSession()->addSuccess(Mage::helper('rma')->__('The RMA status has been deleted.'));
            $this->_redirect('*/*/');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                    $e, Mage::helper('rma')->__('An error occurred while deleting RMA status. The status has not been deleted.')
            );
        }
        $this->_getSession()->setFormData($data);
        $this->_redirect('*/*/edit', array('status_id' => $statusId));

        return;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/rma_statuses');
    }

}