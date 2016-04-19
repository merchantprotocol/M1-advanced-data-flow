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

class AW_Sarp2_Adminhtml_ProfileController extends Mage_Adminhtml_Controller_Action
{
    protected function _initProfile($requestParamName = 'id')
    {
        $profileId = (int)$this->getRequest()->getParam($requestParamName, 0);
        $profile = Mage::getModel('aw_sarp2/profile');
        if ($profileId === 0) {
            throw new Exception($this->__("Undefined param: %s", $requestParamName));
        }
        $profile->load($profileId);
        Mage::register('current_profile', $profile);
        return $this;
    }

    public function indexAction()
    {
        if ($status = $this->getRequest()->getParam('status')) {
            $session = Mage::getSingleton('adminhtml/session');
            // filter id in grid 'awsarp2_profile_gridfilter'
            $filterStringValue = $session->getData('awsarp2_profile_gridfilter');
            if (!is_array($filterStringValue)) {
                $filterArrayValue = Mage::helper('adminhtml')->prepareFilterString($filterStringValue);
            }
            if (array_key_exists($status, Mage::getModel('aw_sarp2/source_profile_status')->toArray())) {
                $filterArrayValue['status'] = $status;
            } else {
                $filterArrayValue = array();
            }
            $session->setData('awsarp2_profile_gridfilter', $filterArrayValue);
            return $this->_redirect('*/*/index/');
        }
        $this->_title($this->__('Subscriptions'))->_title($this->__('Manage Profiles'));
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
        return $this;
    }

    public function viewAction()
    {
        $this->_title($this->__('Subscriptions'))->_title($this->__('View Profile'));
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }

        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
        return $this;
    }

    public function gridAction()
    {
        $this->_initProfile();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function cancelAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        if (is_null(Mage::registry('current_profile')->getId())) {
            $this->_getSession()->addError('Profile can not be loaded');
            return $this->_redirectReferer();
        }
        try {
            Mage::registry('current_profile')->changeStatusToCancel($this->__('Status changed by admin'));
            Mage::getSingleton('core/session')->addSuccess("Profile change status to cancel");
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        return $this->_redirect('*/*/view', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function suspendAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        if (is_null(Mage::registry('current_profile')->getId())) {
            $this->_getSession()->addError('Profile can not be loaded');
            return $this->_redirectReferer();
        }
        try {
            Mage::registry('current_profile')->changeStatusToSuspend($this->__('Status changed by admin'));
            Mage::getSingleton('core/session')->addSuccess("Profile change status to suspend");
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        return $this->_redirect('*/*/view', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function activateAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        if (is_null(Mage::registry('current_profile')->getId())) {
            $this->_getSession()->addError('Profile can not be loaded');
            return $this->_redirectReferer();
        }
        try {
            Mage::registry('current_profile')->changeStatusToActive($this->__('Status changed by admin'));
            Mage::getSingleton('core/session')->addSuccess("Profile change status to active");
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        return $this->_redirect('*/*/view', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function updateAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        if (is_null(Mage::registry('current_profile')->getId())) {
            $this->_getSession()->addError('Profile can not be loaded');
            return $this->_redirectReferer();
        }
        try {
            Mage::registry('current_profile')->synchronizeWithEngine();
            Mage::getSingleton('core/session')->addSuccess("Profile updated");
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        return $this->_redirect('*/*/view', array('id' => Mage::registry('current_profile')->getId()));
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
        return Mage::getSingleton('admin/session')->isAllowed('aw_sarp2/profile');
    }

    public function flushAction()
    {
        $result = array(
            'result' => false,
        );
        if ($this->getRequest()->isAjax()) {
            $profileOrderTable = Mage::getSingleton('core/resource')->getTableName('aw_sarp2/profile_order');
            $profileTable = Mage::getSingleton('core/resource')->getTableName('aw_sarp2/profile');
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $write->delete($profileOrderTable);
                $write->delete($profileTable);
                $result['result'] = true;
            } catch (Exception $e) {
                $result['message'] .= $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}