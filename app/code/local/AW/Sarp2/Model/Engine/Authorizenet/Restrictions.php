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


class AW_Sarp2_Model_Engine_Authorizenet_Restrictions implements AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
{
    /**
     * @return array
     */
    public function getAvailableSubscriptionStatus()
    {
        return array(
            'active', 'expired', 'suspended', 'canceled', 'terminated'
        );
    }

    /**
     * @param string $currentStatus
     *
     * @return array = update|activate|suspend|cancel
     */
    public function getAvailableSubscriptionOperations($currentStatus)
    {
        switch ($currentStatus) {
            case 'active':
            case 'suspended':
                return array('update', 'cancel');
            case 'expired':
            case 'canceled':
            case 'terminated':
                return array();
            default:
                return array();
        }
    }

    /**
     * @return array
     */
    public function getAvailableUnitOfTime()
    {
        return array('days', 'months',);
    }

    /**
     * @return string
     */
    public function getStartDateFormat()
    {
        return 'yyyy-MM-dd';
    }

    /**
     * @return boolean
     */
    public function isTrialSupported()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function isInitialAmountSupported()
    {
        return false;
    }

    /**
     * @param array $data
     * ==================================================
     * $data = array(
     *   'subscription_status' => string,
     *   'subscription_reference' => string,
     *   'subscription_name' => string,
     *   'interval' => array('unit' => string, 'length' => integer),
     *   'start_date' => Zend_Date,
     *   'total_cycles' => integer,
     *   'amount' => float,
     *   'initial_amount' => float,
     *   'currency_code' => string,
     *   'trial_cycles' => integer,
     *   'trial_amount' => float,
     *   'payment_info' => Mage_Payment_Model_Info,
     *   'customer' => Mage_Customer_Model_Customer,
     *   'billing_address' => Mage_Customer_Model_Address_Abstract,
     *   'shipping_address' => Mage_Customer_Model_Address_Abstract
     * )
     * =========================================================
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateAll($data)
    {
        if (isset($data['subscription_status'])) {
            $this->validateSubscriptionStatus($data['subscription_status']);
        }
        if (isset($data['subscription_reference'])) {
            $this->validateSubscriptionReference($data['subscription_reference']);
        }
        if (isset($data['subscription_name'])) {
            $this->validateSubscriptionName($data['subscription_name']);
        }
        if (isset($data['interval']) && isset($data['interval']['unit']) && isset($data['interval']['length'])) {
            $this->validateInterval($data['interval']['unit'], $data['interval']['length']);
        }
        if (isset($data['start_date'])) {
            $this->validateStartDate($data['start_date']);
        }
        if (isset($data['total_cycles'])) {
            $this->validateTotalCycles($data['total_cycles']);
        }
        if (isset($data['amount'])) {
            $this->validateAmount($data['amount']);
        }
        if (isset($data['initial_amount'])) {
            $this->validateInitialAmount($data['initial_amount']);
        }
        if (isset($data['currency_code'])) {
            $this->validateCurrencyCode($data['currency_code']);
        }
        if (isset($data['trial_cycles'])) {
            $this->validateTrialCycles($data['trial_cycles']);
        }
        if (isset($data['trial_amount'])) {
            $this->validateTrialAmount($data['trial_amount']);
        }
        if (isset($data['payment_info'])) {
            $this->validatePaymentInfo($data['payment_info']);
        }
        if (isset($data['customer'])) {
            $this->validateCustomerInfo($data['customer']);
        }
        if (isset($data['billing_address'])) {
            $this->validateBillingAddress($data['billing_address']);
        }
        if (isset($data['shipping_address'])) {
            $this->validateShippingAddress($data['shipping_address']);
        }
        return $this;
    }

    /**
     * @param string $status
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateSubscriptionStatus($status)
    {
        $availableStatuses = $this->getAvailableSubscriptionStatus();
        if (!in_array($status, $availableStatuses)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Status is invalid");
        }
        return $this;
    }

    /**
     * @param string $reference
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateSubscriptionReference($reference)
    {
        if (strlen($reference) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Reference is too long");
        }
        return $this;
    }

    /**
     * @param string $name
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateSubscriptionName($name)
    {
        if (strlen($name) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Name is too long");
        }
        return $this;
    }

    /**
     * @param string $unit
     * @param integer $length
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateInterval($unit, $length)
    {
        $availableUnits = $this->getAvailableUnitOfTime();
        if (!in_array($unit, $availableUnits)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Unit of time is invalid");
        }
        if ($length < 1) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Length is invalid");
        }
        if (
            ($unit == 'days' && ($length < 7 || $length > 365)) || ($unit == 'months' && $length > 12)
        ) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "The interval length cannot exceed 365 days or 12 months. "
                . "The interval length must be 7 to 365 days or 1 to 12 months"
            );
        }
        return $this;
    }

    /**
     * @param Zend_Date $date
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateStartDate(Zend_Date $date)
    {
        $date->setTimezone('US/Mountain');
        //today in UTC/GMT
        $now = new Zend_Date();
        $now->setTimezone('US/Mountain');
        if ($date->compare($now, Zend_Date::DATE_SHORT) === -1) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Start date is less then today");
        }
        return $this;
    }

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateTotalCycles($cycles)
    {
        if (($cycles < 0) || ($cycles > 9999) || !is_int($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Incorrect regular total cycles");
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateAmount($amount)
    {
        if ($amount < 0.01) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular amount. Minimal Amount is 0.01"
            );
        }
        if (strlen($amount) > 15) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular amount. Amount length must be up to 15 digits"
            );
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateInitialAmount($amount)
    {
        throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
            "Incorrect initial amount. Amount not available by Authorize.net"
        );
    }

    /**
     * @param string $currencyCode
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateCurrencyCode($currencyCode)
    {
        return $this;
    }

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateTrialCycles($cycles)
    {
        if ($cycles <= 0 || $cycles > 99 || !is_int($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial cycles"
            );
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateTrialAmount($amount)
    {
        if ($amount < 0.01) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial amount. Minimal trial Amount is 0.01"
            );
        }
        if (strlen($amount) > 15) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial amount. Amount length must be up to 15 digits"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Payment_Model_Info $paymentInfo
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validatePaymentInfo(Mage_Payment_Model_Info $paymentInfo)
    {
        if (strlen($paymentInfo->getCcNumber()) < 13 || strlen($paymentInfo->getCcNumber()) > 16) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect credit card number"
            );
        }
        if (!checkdate($paymentInfo->getCcExpMonth(), 1, $paymentInfo->getCcExpYear())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect credit card expiration date"
            );
        }

        $date = Mage::app()->getLocale()->date();
        if (
            !$paymentInfo->getCcExpYear() ||
            !$paymentInfo->getCcExpMonth()
            || ($date->compareYear($paymentInfo->getCcExpYear()) == 1)
            || (
                $date->compareYear($paymentInfo->getCcExpYear()) == 0
                && ($date->compareMonth($paymentInfo->getCcExpMonth()) == 1)
            )
        ) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Credit card is expired"
            );
        }

        if ($paymentInfo->getCcType() == 'AE' && strlen($paymentInfo->getCcCid()) !== 4) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect credit card CID"
            );
        }
        if ($paymentInfo->getCcType() !== 'AE' && strlen($paymentInfo->getCcCid()) !== 3) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect credit card CID"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateCustomerInfo(Mage_Customer_Model_Customer $customer)
    {
        if (strlen($customer->getData('id')) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. ID more than 20 characters"
            );
        }
        if (is_null($customer->getEmail())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. EMAIL field is required"
            );
        } elseif (strlen($customer->getEmail()) > 255) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. EMAIL more than 255 characters"
            );
        }

        $primaryAddress = $customer->getPrimaryBillingAddress();
        if (
            $primaryAddress && !is_null($primaryAddress->getTelephone())
            && strlen($primaryAddress->getTelephone()) > 25
        ) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. Phone number more than 25 characters"
            );
        }
        if ($primaryAddress && !is_null($primaryAddress->getFax()) && strlen($primaryAddress->getFax()) > 25) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. Fax more than 25 characters"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract $billingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateBillingAddress(Mage_Customer_Model_Address_Abstract $billingAddress)
    {
        if (is_null($billingAddress->getFirstname()) || strlen($billingAddress->getFirstname()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. Firstname more than 50 characters"
            );
        }
        if (is_null($billingAddress->getLastname()) || strlen($billingAddress->getLastname()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. Lastname more than 50 characters"
            );
        }
        if (strlen($billingAddress->getCompany()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. Company more than 50 characters"
            );
        }
        if (strlen($billingAddress->getData('street1')) > 60) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. Address more than 60 characters"
            );
        }
        if (strlen($billingAddress->getCity()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. City more than 40 characters"
            );
        }
        if (strlen($billingAddress->getPostcode()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. ZIP more than 20 characters"
            );
        }
        if (strlen($billingAddress->getCountry()) > 60) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. Country more than 60 characters"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract $shippingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return $this
     */
    public function validateShippingAddress(Mage_Customer_Model_Address_Abstract $shippingAddress)
    {
        if (strlen($shippingAddress->getFirstname()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. Firstname more than 50 characters"
            );
        }
        if (strlen($shippingAddress->getLastname()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. Lastname more than 50 characters"
            );
        }
        if (strlen($shippingAddress->getCompany()) > 50) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. Company more than 50 characters"
            );
        }
        if (strlen($shippingAddress->getData('street1')) > 60) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. Address more than 60 characters"
            );
        }
        if (strlen($shippingAddress->getCity()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. City more than 40 characters"
            );
        }
        if (strlen($shippingAddress->getPostcode()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. ZIP more than 20 characters"
            );
        }
        if (strlen($shippingAddress->getCountry()) > 60) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. Country more than 60 characters"
            );
        }
        return $this;
    }
}