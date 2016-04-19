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

class AW_Sarp2_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    protected function _initProfile($requestParamName = 'id')
    {
        $profileId = (int)$this->getRequest()->getParam($requestParamName, 0);
        $profile = Mage::getModel('aw_sarp2/profile');
        if ($profileId === 0) {
            throw new Exception('Profile ID has not been specified');
        }
        $profile->load($profileId);
        if ($profile->getCustomerId() !== Mage::getSingleton('customer/session')->getId()) {
            throw new Exception('Profile has not been found');
        }
        Mage::register('current_profile', $profile);
        return true;
    }

    public function getProfile()
    {
        return Mage::registry('current_profile');
    }

    public function indexAction()
    {
        $this->loadLayout();

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl(Mage::getUrl('customer/account'));
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Subscriptions'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl(Mage::getUrl('aw_recurring/customer/index'));
        }
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('aw_recurring/customer/index');
        }
        $this->getLayout()
            ->getBlock('head')
            ->setTitle($this->__('Subscription #%s', $this->getProfile()->getReferenceId()));
        $this->renderLayout();
        return $this;
    }

    public function ordersAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl(Mage::getUrl('aw_recurring/customer/index'));
        }
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('aw_recurring/customer/index');
        }
        $this->getLayout()
            ->getBlock('head')
            ->setTitle($this->__('Subscription #%s', $this->getProfile()->getReferenceId()));
        $this->renderLayout();
        return $this;
    }

    public function cancelAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        try {
            Mage::registry('current_profile')->changeStatusToCancel($this->__('Status changed by customer'));
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('*/*/view', array('_current' => true));
        }
        Mage::getSingleton('core/session')->addSuccess('Subscription successfully cancelled');
        return $this->_redirect('*/*/view', array('_current' => true));
    }

    public function suspendAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        try {
            Mage::registry('current_profile')->changeStatusToSuspend($this->__('Status changed by customer'));
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('*/*/view', array('_current' => true));
        }
        Mage::getSingleton('core/session')->addSuccess('Subscription successfully suspended');
        return $this->_redirect('*/*/view', array('_current' => true));
    }

    public function activateAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        try {
            Mage::registry('current_profile')->changeStatusToActive($this->__('Status changed by customer'));
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('*/*/view', array('_current' => true));
        }
        Mage::getSingleton('core/session')->addSuccess('Subscription successfully activated');
        return $this->_redirect('*/*/view', array('_current' => true));
    }

    public function updateAction()
    {
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            return $this->_redirect('*/*/index');
        }
        try {
            Mage::registry('current_profile')->synchronizeWithEngine();
            Mage::getSingleton('core/session')->addSuccess('Subscription successfully updated');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable update subscription');
        }
        return $this->_redirect('*/*/view', array('_current' => true));
    }
}