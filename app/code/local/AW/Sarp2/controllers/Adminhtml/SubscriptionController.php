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

class AW_Sarp2_Adminhtml_SubscriptionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initSubscription($requestParamName = 'id')
    {
        $subscriptionId = (int)$this->getRequest()->getParam($requestParamName, 0);
        $subscription = Mage::getModel('aw_sarp2/subscription');
        if ($subscriptionId > 0) {
            $subscription->load($subscriptionId);
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getSubscriptionFormData()) {
            $subscription->addData($data);
            Mage::getSingleton('adminhtml/session')->setSubscriptionFormData(null);
        }
        Mage::register('current_subscription', $subscription);
        return $this;
    }

    protected function _initProduct($requestParamName = 'product_id')
    {
        $productId = (int)$this->getRequest()->getParam($requestParamName, 0);
        if (Mage::registry('current_subscription') && Mage::registry('current_subscription')->getProductId()) {
            $productId = Mage::registry('current_subscription')->getProductId();
        }
        $product = Mage::getModel('catalog/product');
        if ($productId > 0) {
            $product->load($productId);
        } else {
            throw new Exception('Product not specified');
        }
        Mage::register('current_product', $product);
        Mage::registry('current_subscription')->setProductId($productId);
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Subscriptions'))->_title($this->__('Manage Subscriptions'));
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_title($this->__('Subscriptions'))->_title($this->__('New Subscription'));
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
    }

    public function editAction()
    {
        try {
            $this->_initSubscription();
            $this->_initProduct();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
            return $this->_redirect('*/*/index');
        }

        $this->_title($this->__('Subscriptions'));
        if (Mage::registry('current_subscription')->getId()) {
            $this->_title($this->__('Edit Subscription'));
        } else {
            $this->_title($this->__('Create Subscription'));
        }
        $this->loadLayout();
        $this->_setActiveMenu('aw_sarp2/subscription');
        $this->renderLayout();
        return $this;
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            $this->_initSubscription();
            $this->_initProduct();
            $subscription = Mage::registry('current_subscription');
            $subscription->addData($data);

            $deletedItemIds = array_diff(
                $subscription->getItemCollection()->getAllIds(),
                array_keys($data['subscription_type'])
            );
            foreach ($deletedItemIds as $itemId) {
                $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item');
                $subscriptionItem->load($itemId);
                $subscriptionItem->delete();
            }
            $subscriptionItems = array();
            foreach ($data['subscription_type'] as $key => $value) {
                $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item');
                if (strpos($key, 'new') === false) {
                    $subscriptionItem->load($key);
                }
                if (!isset($value['trial_price'])) {
                    $value['trial_price'] = null;
                }
                if (!isset($value['initial_fee_price'])) {
                    $value['initial_fee_price'] = null;
                }
                $subscriptionItem->addData($value);
                $subscriptionItems[] = $subscriptionItem;
            }
            //+++validation start
            $validationErrors = array();
            foreach ($subscriptionItems as $item) {
                try {
                    $item->validate();
                } catch (AW_Sarp2_Model_Subscription_TypeException $e) {
                    $validationErrors[] = $e->getMessage();
                }
            }
            //---validation end
            if (count($validationErrors) == 0) {
                $subscription->save();
                foreach ($subscriptionItems as $item) {
                    $item->setSubscriptionId($subscription->getId());
                    $item->save();
                }
            } else {
                foreach ($validationErrors as $error) {
                    $this->_getSession()->addError($this->__($error));
                }
                Mage::getSingleton('adminhtml/session')->setSubscriptionFormData($data);
                return $this->_redirect('*/*/edit', array('_current' => true, 'id' => $subscription->getId()));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Unable save subscription'));
            Mage::getSingleton('adminhtml/session')->setSubscriptionFormData($data);
            return $this->_redirect(
                '*/*/edit', array('_current' => true, 'id' => Mage::registry('current_subscription')->getId())
            );
        }

        $this->_getSession()->addSuccess(
            $this->__(
                'Subscription for product "%s" has been successfully saved',
                Mage::registry('current_product')->getName()
            )
        );
        Mage::getSingleton('adminhtml/session')->setSubscriptionFormData(null);
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', array('_current' => true, 'id' => $subscription->getId()));
        }
        return $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        try {
            $this->_initSubscription();
            $this->_initProduct();
            $subscription = Mage::registry('current_subscription');
            foreach ($subscription->getItemCollection()->getAllIds() as $itemId) {
                $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item');
                $subscriptionItem->load($itemId);
                $subscriptionItem->delete();
            }
            $subscription->delete();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Unable remove subscription'));
            return $this->_redirect('*/*/edit', array('id' => Mage::registry('current_subscription')->getId()));
        }
        $this->_getSession()->addSuccess(
            $this->__(
                'Subscription for product "%s" has been successfully deleted',
                Mage::registry('current_product')->getName()
            )
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
        return Mage::getSingleton('admin/session')->isAllowed('aw_sarp2/subscription');
    }
}