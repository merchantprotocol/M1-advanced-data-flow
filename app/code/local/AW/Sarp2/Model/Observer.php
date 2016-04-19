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


class AW_Sarp2_Model_Observer
{
    public function predispatchCheckoutCartAdd($observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        
        $request = $observer->getControllerAction()->getRequest();
        $productId = $request->getParam('product', null);
        $product = Mage::getModel('catalog/product')->load($productId);
        $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($productId);
        if (is_null($subscription->getId()) || is_null($product->getId())) {
            return $this;
        }
        if ($subscription->getData('is_subscription_only')) {
            $options = $request->getParam('options', array());
            if (!isset($options[AW_Sarp2_Helper_Subscription::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID])) {
                $notice = Mage::helper('catalog')->__('Please specify the product\'s required option(s).');
                Mage::getSingleton('catalog/session')->addNotice($notice);
                $response = $observer->getControllerAction()->getResponse();
                $response->setRedirect($product->getProductUrl())->sendResponse();
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                return $this;
            }
        }
        return $this;
    }

    public function predispatchCheckoutCartCouponPost($observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($quote->getItemsCollection() as $quoteItem) {
            if (!Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($quoteItem)) {
                continue;
            }
            Mage::getSingleton('checkout/session')->addNotice(
                Mage::helper('aw_sarp2')->__('Coupons can not be applied to subscriptions')
            );
            $response = $observer->getControllerAction()->getResponse();
            $response->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    public function catalogProductTypePrepareFullOptions($observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        $buyRequest = $observer->getEvent()->getBuyRequest();
        $transport = $observer->getEvent()->getTransport();
        $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($product->getId());
        if (is_null($subscription->getId())) {
            return $this;
        }

        $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')
            ->getSubscriptionTypeOptionFromBuyRequest($buyRequest)
        ;
        $subscriptionStartDateOption = Mage::helper('aw_sarp2/quote')
            ->getSubscriptionStartDateOptionFromBuyRequest($buyRequest)
        ;
        if (is_null($subscriptionTypeOption) && is_null($subscriptionStartDateOption)) {
            return $this;
        }
        if (is_null($subscriptionStartDateOption)) {
            $calculatedStartDate = Mage::helper('aw_sarp2/subscription')
                ->calculateSubscriptionStartDateForSelectedDate($subscription)
            ;
            if (Mage::getSingleton('catalog/product_option_type_date')->useCalendar()) {
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                $date = $calculatedStartDate->toString($format);
            } else {
                $date = array(
                    'year' => $calculatedStartDate->getYear(),
                    'month' => $calculatedStartDate->getMonth(),
                    'day' => $calculatedStartDate->getDay(),
                );
            }
            $options = $buyRequest['options'];
            $options[AW_Sarp2_Helper_Subscription::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID] = $date;
            $buyRequest->setData('options', $options);
            $subscriptionStartDateOption = Mage::helper('aw_sarp2/quote')
                ->getSubscriptionStartDateOptionFromBuyRequest($buyRequest)
            ;
        }
        $transport->options[AW_Sarp2_Helper_Subscription::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID]
            = $subscriptionTypeOption;
        $transport->options[AW_Sarp2_Helper_Subscription::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID]
            = $subscriptionStartDateOption;
        return $this;
    }


    /**
     * Add item to quote
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  AW_Sarp2_Model_Observer
     */
    public function salesQuoteAddItem($observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        $item = $observer->getEvent()->getQuoteItem();
        $quote = $item->getQuote();
        $isSubscriptionProduct = Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($item);
        $itemParentProductId = $item->getProduct()->getData('parent_product_id');

        $quoteHasSubscriptionItem = false;
        foreach ($quote->getItemsCollection() as $quoteItem) {
            if ($item->compare($quoteItem)) {
                continue;
            }
            //is current item has parent
            if (!is_null($itemParentProductId) && $itemParentProductId == $quoteItem->getProduct()->getId()) {
                continue;
            }
            //is item has parent and it equal current item parent
            $quoteItemParentProductId = $quoteItem->getProduct()->getData('parent_product_id');
            if (!is_null($quoteItemParentProductId) && ($quoteItemParentProductId === $itemParentProductId)) {
                continue;
            }
            $quoteHasSubscriptionItem = $quoteHasSubscriptionItem
                || Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($quoteItem);
        }
        if ($isSubscriptionProduct && !is_null($itemParentProductId)) {
            Mage::throwException(
                Mage::helper('aw_sarp2')->__('Subscription item can not be a child of composite product')
            );
        }
        if (($quote->getItemsCount() > 0 && $isSubscriptionProduct) || $quoteHasSubscriptionItem) {
            Mage::throwException(
                Mage::helper('aw_sarp2')->__(
                    'Subscription item can be purchased standalone only. '
                    . 'To proceed please remove other items from the cart.'
                )
            );
        }
        /* validate options */
        $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')->getSubscriptionTypeOptionFromQuoteItem($item);
        $subscriptionStartDateOption = Mage::helper('aw_sarp2/quote')
            ->getSubscriptionStartDateOptionFromQuoteItem($item)
        ;
        //is option has been selected
        if ($subscriptionTypeOption) {
            try {
                $selectedDate = Mage::app()->getLocale()->storeDate(null, null, false);
                if (null !== $subscriptionStartDateOption) {
                    $selectedDate->setDate($subscriptionStartDateOption);
                }
            } catch (Zend_Date_Exception $e) {
                Mage::throwException(
                    Mage::helper('aw_sarp2')->__('Selected date is not valid for specified period')
                );
            }
            $todayDate = Mage::app()->getLocale()->storeDate(null, null, false);
            if ($selectedDate->compare($todayDate, Zend_Date::DATE_SHORT) < 0) {
                Mage::throwException(
                    Mage::helper('aw_sarp2')->__('Selected date is not valid for specified period')
                );
            }
        }
        return $this;
    }

    /**
     * Observer for add our options to product items after load collection
     * in Mage_Sales_Model_Resource_Quote_Item_Collection->_assignProducts
     * @param $observer
     *
     * @return $this
     */
    public function salesQuoteItemCollectionProductsAfterLoad($observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        $productCollection = $observer->getEvent()->getProductCollection();
        foreach ($productCollection as $product) {
            $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($product->getId());
            if (is_null($subscription->getId())) {
                return $this;
            }
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if (!$quote) {
                return $this;
            }
            $quoteItem = null;
            foreach ($quote->getAllItems() as $item) {
                if ($item->getProduct()->getId() == $product->getId()) {
                    $quoteItem = $item;
                }
            }
            if (null === $quoteItem || !Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($quoteItem)) {
                return $this;
            }
            /** @var AW_Sarp2_Helper_Subscription $helper */
            $helper = Mage::helper('aw_sarp2/subscription');
            $helper->addSubscriptionTypeSelectorToSubscription(
                $subscription, $product, Mage::helper('aw_sarp2')->__('Subscription Type')
            );
            $helper->addSubscriptionStartDateLabelOptionToSubscription(
                $subscription, $product, Mage::helper('aw_sarp2')->__('Start date')
            );
            $product->setHasOptions(true);
            $product->setRequiredOptions(true);
        }
        return $this;
    }

    /**
     * Add options to product view
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  AW_Sarp2_Model_Observer
     */
    public function catalogControllerProductView(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return $this;
        }
        /** @var AW_Sarp2_Helper_Subscription $helper */
        $helper = Mage::helper('aw_sarp2/subscription');
        $product = $observer->getEvent()->getProduct();
        $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($product->getId());
        if (is_null($subscription->getId())) {
            return $this;
        }
        $helper->addSubscriptionTypeSelectorToSubscription(
            $subscription, $product, Mage::helper('aw_sarp2')->__('Subscription Type')
        );
        $helper->addSubscriptionStartDateOptionToSubscription(
            $subscription, $product, Mage::helper('aw_sarp2')->__('Start date')
        );

        /** ++ hack for displaying options block for product without options*/
        $product->setHasOptions(true);
        /** -- hack for displaying options block for product without options*/
        if ($subscription->getIsSubscriptionOnly()) {
            $subscriptionItemCollection = $helper->getSubscriptionItemCollectionForDisplayingOnStore($subscription);
            foreach ($subscriptionItemCollection as $item) {
                $product->setPrice($item->getRegularPrice());
                break;
            }
        }
        //flag for get final price
        $this->_addCatalogControllerProductViewFlag();
        return $this;
    }

    /**
     * Apply price for subscription
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  AW_Sarp2_Model_Observer
     */
    public function catalogProductGetFinalPrice(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('aw_sarp2')->isEnabled() || !$this->_isCatalogControllerProductViewFlag()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($product->getId());
        if (is_null($subscription->getId())) {
            return $this;
        }
        if ($subscription->getIsSubscriptionOnly()) {
            $subscriptionItemCollection = Mage::helper('aw_sarp2/subscription')
                ->getSubscriptionItemCollectionForDisplayingOnStore($subscription);
            foreach ($subscriptionItemCollection as $item) {
                $product->setPrice($item->getRegularPrice());
                $product->setFinalPrice($product->getPrice());
                break;
            }
        }
        return $this;
    }

    public function paymentMethodIsActive(Varien_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getMethodInstance();
        $quote = $observer->getEvent()->getQuote();
        $result = $observer->getEvent()->getResult();
        if (!$quote) {
            return;
        }
        $isQuoteHasSubscriptionProduct = Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($quote);
        $availablePaymentMethods = array();
        foreach (Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($quote) as $item) {

            $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')->getSubscriptionTypeOptionFromQuoteItem($item);
            $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item')->load($subscriptionTypeOption);
            $engineModel = $subscriptionItem->getTypeModel()->getEngineModel();

            $availablePaymentMethods = array_merge(
                $availablePaymentMethods,
                Mage::helper('aw_sarp2/engine')->getPaymentMethodsByEngine($engineModel)
            );
        }
        if ($isQuoteHasSubscriptionProduct && !in_array($method->getCode(), $availablePaymentMethods)) {
            $result->isAvailable = false;
        }
    }

    /**
     * move customer to group functionality
     *
     * @param $observer
     */
    public function profileSaveAfter($observer)
    {
        $profile = $observer->getEvent()->getDataObject();
        if ($profile->getOrigData('status') === $profile->getData('status')) {
            //is status has not been changed
            return;
        }
        $newStatus = $profile->getData('status');
        $globalMap = $profile->getSubscriptionEngineModel()->getStatusSource()->getGlobalStatusMap();
        if (
            array_key_exists(AW_Sarp2_Model_Source_Profile_Status::ACTIVE, $globalMap)
            && $globalMap[AW_Sarp2_Model_Source_Profile_Status::ACTIVE] === $newStatus
        ) {
            //status changed to active
            $customerId = $profile->getData('customer_id');
            $groupId = $profile->getData('details/subscription/general/move_customer_to_group_id');
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if (is_null($customer->getId())) {
                return;
            }
            if (($groupId === "0") || ($customer->getGroupId() == $groupId)) {
                return;
            }

            $groupLinkCollection = Mage::getModel('aw_sarp2/customer_group')->getCollection()
                ->addCustomerFilter($customer)
            ;
            //if customer has been linked then get native group id
            $linkedGroupId = $customer->getGroupId();
            if ($groupLinkCollection->getSize() > 0) {
                $linkedGroupId = $groupLinkCollection->getFirstItem()->getData('group_id');
            }
            $groupLink = Mage::getModel('aw_sarp2/customer_group')->setData(
                array(
                     'profile_id' => $profile->getId(),
                     'customer_id' => $customer->getId(),
                     'group_id' => $linkedGroupId
                )
            );
            $customer->setGroupId($groupId);
            try {
                $customer->save();
                $groupLink->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        if (
            array_key_exists(AW_Sarp2_Model_Source_Profile_Status::CANCELLED, $globalMap)
            && $globalMap[AW_Sarp2_Model_Source_Profile_Status::CANCELLED] === $newStatus
        ) {
            //status changed to cancel
            $customerId = $profile->getData('customer_id');
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if (is_null($customer->getId())) {
                return;
            }
            $groupLink = Mage::getModel('aw_sarp2/customer_group')->loadByProfileId($profile->getId());
            if (is_null($groupLink->getId())) {
                return;
            }
            $groupLinkCollection = Mage::getModel('aw_sarp2/customer_group')->getCollection()
                ->addCustomerFilter($customer)
            ;
            if ($groupLinkCollection->getSize() === 1) {
                try {
                    //move to another group if customer not linked to another subscription
                    $customer->setGroupId($groupLink->getGroupId());
                    $customer->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            try {
                $groupLink->delete();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    protected function _addCatalogControllerProductViewFlag()
    {
        Mage::register('aw_sarp2_catalog_controller_product_view_flag', true);
        return $this;
    }

    protected function _isCatalogControllerProductViewFlag()
    {
        return !is_null(Mage::registry('aw_sarp2_catalog_controller_product_view_flag'));
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    protected function _getProductUrl(Mage_Catalog_Model_Product $product)
    {
        $query = array();
        return $product->getUrlModel()->getUrl(
            $product, array('_query' => $query, '_secure' => Mage::app()->getRequest()->isSecure())
        );
    }
}