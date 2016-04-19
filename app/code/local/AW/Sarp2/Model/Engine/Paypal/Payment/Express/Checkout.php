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


class AW_Sarp2_Model_Engine_Paypal_Payment_Express_Checkout extends Mage_Paypal_Model_Express_Checkout
{
    public function start($returnUrl, $cancelUrl)
    {
        $this->_quote->collectTotals();

        #AW_SARP2 override start
        $isQuoteHasSubscriptionProduct = Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($this->_quote);
        if (!$this->_quote->getGrandTotal() && !$this->_quote->hasNominalItems() && !$isQuoteHasSubscriptionProduct) {
            Mage::throwException(
                Mage::helper('paypal')->__(
                    'PayPal does not support processing orders with zero amount. '
                    . 'To complete your purchase, proceed to the standard checkout process.'
                )
            );
        }
        #AW_SARP2 override end

        $this->_quote->reserveOrderId()->save();
        // prepare API
        $this->_getApi();
        $this->_api->setAmount($this->_quote->getBaseGrandTotal())
            ->setCurrencyCode($this->_quote->getBaseCurrencyCode())
            ->setInvNum($this->_quote->getReservedOrderId())
            ->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl)
            ->setSolutionType($this->_config->solutionType)
            ->setPaymentAction($this->_config->paymentAction);
        if ($this->_giropayUrls) {
            list($successUrl, $cancelUrl, $pendingUrl) = $this->_giropayUrls;
            $this->_api->addData(
                array(
                     'giropay_cancel_url'           => $cancelUrl,
                     'giropay_success_url'          => $successUrl,
                     'giropay_bank_txn_pending_url' => $pendingUrl,
                )
            );
        }

        $this->_setBillingAgreementRequest();

        if (
            defined('Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_ALL')
            && $this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_ALL
        ) {
            $this->_api->setRequireBillingAddress(1);
        }

        // supress or export shipping address
        if ($this->_quote->getIsVirtual()) {
            if (
                defined('Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_VIRTUAL')
                && $this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_VIRTUAL
            ) {
                $this->_api->setRequireBillingAddress(1);
            }
            $this->_api->setSuppressShipping(true);
        } else {
            $address = $this->_quote->getShippingAddress();
            $isOverriden = 0;
            if (true === $address->validate()) {
                $isOverriden = 1;
                $this->_api->setAddress($address);
            }
            $this->_quote->getPayment()->setAdditionalInformation(
                Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN, $isOverriden
            );
            $this->_quote->getPayment()->save();
        }

        /*hack for matrixrate*/
        if ($isQuoteHasSubscriptionProduct) {
            $currentAddress = $this->_quote->getBillingAddress();
            if (!$this->_quote->getIsVirtual()) {
                $currentAddress = $this->_quote->getShippingAddress();
            }
            $originalBaseShippingAmount = $currentAddress->getBaseShippingAmount();
            $currentAddress->setBaseShippingAmount(0.0);
        }
        /* end hack for matrixrate*/

        // add line items
        if (!method_exists(Mage::helper('paypal'), 'prepareLineItems')) {
            /**
             * @var Mage_Paypal_Model_Cart $paypalCart
             */
            $paypalCart = Mage::getModel('paypal/cart', array($this->_quote));
            $this->_api->setPaypalCart($paypalCart)->setIsLineItemsEnabled($this->_config->lineItemsEnabled);
        } else {
            list($items, $totals) = Mage::helper('paypal')->prepareLineItems($this->_quote);
            if (Mage::helper('paypal')->areCartLineItemsValid($items, $totals, $this->_quote->getBaseGrandTotal())) {
                $this->_api->setLineItems($items)->setLineItemTotals($totals);
            }
        }

        // add shipping options if needed and line items are available
        if (
            $this->_config->lineItemsEnabled
            && $this->_config->transferShippingOptions
            && (!isset($paypalCart) || $paypalCart->getItems())
        ) {
            #AW_SARP2 override start
            if (
                !$this->_quote->getIsVirtual() && !$this->_quote->hasNominalItems()
                && !$isQuoteHasSubscriptionProduct
            ) {
                if ($options = $this->_prepareShippingOptions($address, true)) {
                    $this->_api->setShippingOptionsCallbackUrl(
                        Mage::getUrl('*/*/shippingOptionsCallback', array('quote_id' => $this->_quote->getId()))
                    )->setShippingOptions($options);
                }
            }
            #AW_SARP2 override end
        }

        // add recurring payment profiles information
        if ($profiles = $this->_quote->prepareRecurringPaymentProfiles()) {
            foreach ($profiles as $profile) {
                $profile->setMethodCode(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
                if (!$profile->isValid()) {
                    Mage::throwException($profile->getValidationErrors(true, true));
                }
            }
            $this->_api->addRecurringPaymentProfiles($profiles);
        }

        #AW_SARP2 override start
        if ($isQuoteHasSubscriptionProduct) {
            $subscriptionItems = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($this->_quote);
            $profiles = array();
            foreach ($subscriptionItems as $item) {
                $profile = new Varien_Object();
                $profile->setData(
                    array(
                         'schedule_description' => Mage::helper('aw_sarp2/profile')->generateDescription($item)
                    )
                );
                $profiles[] = $profile;
            }
            $this->_api->addRecurringPaymentProfiles($profiles);
        }
        #AW_SARP2 override end

        $this->_config->exportExpressCheckoutStyleSettings($this->_api);

        // call API and redirect with token
        $this->_api->callSetExpressCheckout();
        $token = $this->_api->getToken();
        $this->_redirectUrl = $this->_config->getExpressCheckoutStartUrl($token);

        /*hack for matrixrate*/
        if ($isQuoteHasSubscriptionProduct) {
            $currentAddress->setBaseShippingAmount($originalBaseShippingAmount);
        }
        /* end hack for matrixrate*/

        $this->_quote->getPayment()->unsAdditionalInformation(
            Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT
        );
        $this->_quote->getPayment()->save();
        return $token;
    }

    public function returnFromPaypal($token)
    {
        $originalBillingAddress = clone $this->_quote->getBillingAddress();
        parent::returnFromPaypal($token);
        $billingAddress = $this->_quote->getBillingAddress();
        $exportedBillingAddress = $this->_api->getExportedBillingAddress();
        foreach ($exportedBillingAddress->getExportedKeys() as $key) {
            $originalValue = $originalBillingAddress->getDataUsingMethod($key);
            $newValue = $billingAddress->getDataUsingMethod($key);
            $exportValue = $exportedBillingAddress->getData($key);
            if (is_null($exportValue) && is_null($newValue) && !is_null($originalValue)) {
                $billingAddress->setDataUsingMethod($key, $originalValue);
            }
        }
        $this->_quote->setTotalsCollectedFlag(false);
        $this->_quote->collectTotals()->save();
    }

    public function place($token, $shippingMethodCode = null)
    {
        $isQuoteHasSubscriptionProduct = Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($this->_quote);
        if (!$isQuoteHasSubscriptionProduct) {
            return parent::place($token, $shippingMethodCode);
        }

        if ($shippingMethodCode) {
            $this->updateShippingMethod($shippingMethodCode);
        }

        if (method_exists($this, 'getCheckoutMethod')) {
            $isNewCustomer = false;
            switch ($this->getCheckoutMethod()) {
                case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
                    $this->_prepareGuestQuote();
                    break;
                case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
                    $this->_prepareNewCustomerQuote();
                    $isNewCustomer = true;
                    break;
                default:
                    $this->_prepareCustomerQuote();
                    break;
            }
        }

        $this->_ignoreAddressValidation();
        $this->_quote->collectTotals();
        #AW_SARP2 override start
        $service = Mage::getModel('aw_sarp2/sales_service_profile', $this->_quote);
        $service->submitProfile();
        #AW_SARP2 override end
        $this->_quote->save();
        if (isset($isNewCustomer) && $isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $this->_recurringPaymentProfiles = $service->getRecurringPaymentProfiles();
        return $this;
    }

    /**
     * Setter that enables giropay redirects flow
     *
     * @param string $successUrl - payment success result
     * @param string $cancelUrl  - payment cancellation result
     * @param string $pendingUrl - pending payment result
     *
     * @return $this
     */
    public function prepareGiropayUrls($successUrl, $cancelUrl, $pendingUrl)
    {
        //it is the best way for override this
        $cancelUrl = Mage::getUrl('aw_recurring/express/cancel');
        $this->_giropayUrls = array($successUrl, $cancelUrl, $pendingUrl);
        return $this;
    }

    private function _ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
            if (!$this->_config->requireBillingAddress && !$this->_quote->getBillingAddress()->getEmail()) {
                $this->_quote->getBillingAddress()->setSameAsBilling(1);
            }
        }
    }
}