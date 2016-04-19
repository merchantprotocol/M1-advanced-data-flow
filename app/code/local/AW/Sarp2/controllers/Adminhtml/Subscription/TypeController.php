<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Sarp2_Adminhtml_Subscription_TypeController extends Mage_Adminhtml_Controller_Action
{
    protected function _initType($requestParamName = 'id')
    {
        $typeId = (int)$this->getRequest()->getParam($requestParamName, 0);
        $type = Mage::getModel('aw_sarp2/subscription_type');
        if ($typeId > 0) {
            $type->load($typeId);
        } elseif ($engineCode = $this->getRequest()->getParam('engine_code', null)) {
            $type->setEngineCode($engineCode);
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getSubscriptionTypeFormData()) {
            $type->addData($data);
            Mage::getSingleton('adminhtml/session')->setSubscriptionTypeFormData(null);
        }
        if (is_null($type->getEngineCode())) {
            throw new Exception('Engine not specified');
        }
        Mage::register('current_type', $type);
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Subscriptions'))->_title($this->__('Manage Subscription Types'));
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_title($this->__('Subscriptions'))->_title($this->__('New Subscription Type'));
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
    }

    public function editAction()
    {
        try {
            $this->_initType();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__("Unable initialize subscription type"));
            return $this->_redirect('*/*/index');
        }
        $this->_title($this->__('Subscriptions'));
        if (Mage::registry('current_type')->getId()) {
            $this->_title($this->__('Edit Subscription Type'));
        } else {
            $this->_title($this->__('Create Subscription Type'));
        }
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
        return $this;
    }

    public function gridAction()
    {
        try {
            $this->_initType();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__("Unable initialize subscription type"));
            return $this->_redirect('*/*/grid');
        }
        $this->loadLayout();
        $this->renderLayout();
        return $this;
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getParams();
        try {
            $this->_initType();
            if (Mage::registry('current_type')->getId()) {
                $type = Mage::registry('current_type');
            } else {
                $type = Mage::getModel('aw_sarp2/subscription_type');
            }
            $type->addData($data);
            $type->validate();
            $type->save();
        } catch (AW_Sarp2_Model_Subscription_TypeException $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
            Mage::getSingleton('adminhtml/session')->setSubscriptionTypeFormData($data);
            return $this->_redirect(
                '*/*/edit', array('_current' => true, 'id' => Mage::registry('current_type')->getId())
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__("Unable save subscription type"));
            Mage::getSingleton('adminhtml/session')->setSubscriptionTypeFormData($data);
            if (Mage::registry('current_type') instanceof AW_Sarp2_Model_Subscription_Type) {
                $typeId = Mage::registry('current_type')->getId();
            } else {
                $typeId = 0;
            }
            return $this->_redirect('*/*/edit', array('_current' => true, 'id' => $typeId));
        }

        $this->_getSession()->addSuccess(
            $this->__('Subscription type "%s" has been successfully saved', $type->getTitle())
        );
        if (!is_null($type->getOrigData())) {
            $this->_getSession()->addNotice(
                $this->__(
                    'Please, check the subscriptions which are related to the modified type "%s"', $type->getTitle()
                )
            );
        }
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', array('_current' => true, 'id' => $type->getId()));
        }
        return $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        try {
            $this->_initType();
            $type = Mage::registry('current_type');
            $type->delete();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__("Unable remove subscription type"));
            return $this->_redirect('*/*/edit', array('id' => Mage::registry('current_type')->getId()));
        }
        $this->_getSession()->addSuccess(
            $this->__('Subscription type "%s" has been successfully deleted', $type->getTitle())
        );
        return $this->_redirect('*/*/index');
    }

    protected function _title($text = null, $resetIfExists = false)
    {
        if (Mage::helper('aw_sarp2')->checkMageVersion()) {
            return parent::_title($text, $resetIfExists);
        }
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('aw_sarp2/subscription_type');
    }
}