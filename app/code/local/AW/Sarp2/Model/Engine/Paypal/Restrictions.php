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


class AW_Sarp2_Model_Engine_Paypal_Restrictions implements AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
{
    /**
     * @return array
     */
    public function getAvailableSubscriptionStatus()
    {
        return array(
            'Active', 'Pending', 'Cancelled', 'Suspended', 'Expired'
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
            case 'Active':
                return array('update', 'suspend', 'cancel');
            case 'Suspended':
                return array('update', 'activate', 'cancel');
            case 'Pending':
            case 'Expired':
            case 'Cancelled':
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
        return array('Day', 'Week', 'SemiMonth', 'Month', 'Year');
    }

    /**
     * @return string
     */
    public function getStartDateFormat()
    {
        //ISO format
        return 'yyyy-MM-ddThh:mm:ss';
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
        return true;
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
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
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
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
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
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateSubscriptionReference($reference)
    {
        if (strlen($reference) > 127) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Reference is too long: 127 characters is maximal"
            );
        }
        return $this;
    }

    /**
     * @param string $name
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateSubscriptionName($name)
    {
        if (preg_match('/[^A-Za-z0-9 ]/', $name) > 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Name is no single byte");
        }
        if (strlen($name) > 32) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Name is too long: 32 characters is maximal");
        }
        return $this;
    }

    /**
     * @param string  $unit
     * @param integer $length
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
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
            ($unit == 'Day' && $length > 365)
            || ($unit == 'Week' && $length > 52)
            || ($unit == 'Month' && $length > 12)
            || ($unit == 'Year' && $length > 1)
        ) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "The combination of time unit and length cannot exceed one year for PayPal"
            );
        }
        if ($unit == 'SemiMonth' && $length != 1) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Length must be 1 for SemiMonth in PayPal"
            );
        }
        return $this;
    }

    /**
     * @param Zend_Date $date
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateStartDate(Zend_Date $date)
    {
        //today in UTC/GMT
        $now = new Zend_Date();
        if ($date->compare($now, Zend_Date::DATE_SHORT) === -1) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException("Start date is less then today");
        }
        return $this;
    }

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateTotalCycles($cycles)
    {
        if (!is_numeric($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular total cycles: total cycles must be a number"
            );
        }
        if (!is_int($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular total cycles: total cycles must be a integer"
            );
        }
        if ($cycles < 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular total cycles: total cycles must be positive"
            );
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateAmount($amount)
    {
        if ($amount <= 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect regular amount. Amount must be positive"
            );
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateInitialAmount($amount)
    {
        if ($amount <= 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect initial amount. Amount must be positive"
            );
        }
        return $this;
    }

    /**
     * @param string $currencyCode
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateCurrencyCode($currencyCode)
    {
        return $this;
    }

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateTrialCycles($cycles)
    {
        if (!is_numeric($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial total cycles: total cycles must be a number"
            );
        }
        if (!is_int($cycles)) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial total cycles: total cycles must be a integer"
            );
        }
        if ($cycles < 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial total cycles: total cycles must be positive"
            );
        }
        return $this;
    }

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateTrialAmount($amount)
    {
        if ($amount < 0) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect trial amount. Amount must be positive"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Payment_Model_Info $paymentInfo
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validatePaymentInfo(Mage_Payment_Model_Info $paymentInfo)
    {
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateCustomerInfo(Mage_Customer_Model_Customer $customer)
    {
        //check email
        if (is_null($customer->getEmail())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. EMAIL field is required"
            );
        } elseif (strlen($customer->getEmail()) > 127) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. EMAIL more than 127 characters"
            );
        } elseif (!Zend_Validate::is($customer->getEmail(), 'EmailAddress')) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. EMAIL must be a valid email address"
            );
        }
        //check payer paypal id
        if (!is_null($customer->getData('payer_id')) && strlen($customer->getData('payer_id')) > 13) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. PAYERID more than 13 characters"
            );
        }
        //check payer status
        if (
            !is_null($customer->getData('payer_status'))
            && !in_array($customer->getData('payer_status'), array('verified', 'unverified'))
        ) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. PAYERSTATUS has incorrect value"
            );
        }
        //check payer business name
        $primaryAddress = $customer->getPrimaryBillingAddress();
        if ($primaryAddress && !is_null($primaryAddress->getCompany()) && strlen($primaryAddress->getCompany()) > 127) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. BUSINESS more than 127 characters"
            );
        }
        //check payer name
        if (!is_null($customer->getPrefix()) && strlen($customer->getPrefix()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. SALUTATION more than 20 characters"
            );
        }
        if (!is_null($customer->getFirstname()) && strlen($customer->getFirstname()) > 25) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. FIRSTNAME more than 25 characters"
            );
        }
        if (!is_null($customer->getMiddlename()) && strlen($customer->getMiddlename()) > 25) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. MIDDLENAME more than 25 characters"
            );
        }
        if (!is_null($customer->getLastname()) && strlen($customer->getLastname()) > 25) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. LASTNAME more than 25 characters"
            );
        }
        if (!is_null($customer->getSuffix()) && strlen($customer->getSuffix()) > 12) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect customer info. SUFFIX more than 12 characters"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract $billingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateBillingAddress(Mage_Customer_Model_Address_Abstract $billingAddress)
    {
        //check street
        if (is_null($billingAddress->getStreet1())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. STREET field is required"
            );
        } elseif (strlen($billingAddress->getStreet1()) > 100) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. STREET more than 100 characters"
            );
        }
        //check street2
        if (!is_null($billingAddress->getStreet2()) && strlen($billingAddress->getStreet2()) > 100) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. STREET2 more than 100 characters"
            );
        }
        //check city
        if (is_null($billingAddress->getCity())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. CITY field is required"
            );
        } elseif (strlen($billingAddress->getCity()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. CITY more than 40 characters"
            );
        }
        //check state
        if (is_null($billingAddress->getRegion())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. STATE field is required"
            );
        } elseif (strlen($billingAddress->getRegion()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. STATE more than 40 characters"
            );
        }
        //check country code
        if (is_null($billingAddress->getCountryId())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. COUNTRYCODE field is required"
            );
        } elseif (strlen($billingAddress->getCountryId()) > 2) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. COUNTRYCODE more than 2 characters"
            );
        }
        //check zip
        if (is_null($billingAddress->getPostcode())) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. ZIP field is required"
            );
        } elseif (strlen($billingAddress->getPostcode()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. ZIP more than 20 characters"
            );
        }
        //check phonenum
        if (!is_null($billingAddress->getTelephone()) && strlen($billingAddress->getTelephone()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect billing address. PHONENUM more than 20 characters"
            );
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract $shippingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    public function validateShippingAddress(Mage_Customer_Model_Address_Abstract $shippingAddress)
    {
        //check street
        if (strlen($shippingAddress->getName()) > 32) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTONAME more than 32 characters"
            );
        }
        //check street
        if (strlen($shippingAddress->getStreet1()) > 100) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOSTREET more than 100 characters"
            );
        }
        //check street2
        if (!is_null($shippingAddress->getStreet2()) && strlen($shippingAddress->getStreet2()) > 100) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOSTREET2 more than 100 characters"
            );
        }
        //check city
        if (strlen($shippingAddress->getCity()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOCITY more than 40 characters"
            );
        }
        //check state
        if (strlen($shippingAddress->getRegion()) > 40) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOSTATE more than 40 characters"
            );
        }
        //check country code
        if (strlen($shippingAddress->getCountryId()) > 2) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOCOUNTRY more than 2 characters"
            );
        }
        //check zip
        if (strlen($shippingAddress->getPostcode()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOZIP more than 20 characters"
            );
        }
        //check phonenum
        if (!is_null($shippingAddress->getTelephone()) && strlen($shippingAddress->getTelephone()) > 20) {
            throw new AW_Sarp2_Model_Engine_PaymentRestrictionsException(
                "Incorrect shipping address. SHIPTOPHONENUM more than 20 characters"
            );
        }
        return $this;
    }
}