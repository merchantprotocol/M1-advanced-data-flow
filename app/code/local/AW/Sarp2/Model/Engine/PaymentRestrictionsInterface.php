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


interface AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
{
    /**
     * @return array
     */
    public function getAvailableSubscriptionStatus();

    /**
     * @param string $currentStatus
     *
     * @return array = update|activate|suspend|cancel
     */
    public function getAvailableSubscriptionOperations($currentStatus);

    /**
     * @return array
     */
    public function getAvailableUnitOfTime();

    /**
     * @return string
     */
    public function getStartDateFormat();

    /**
     * @return boolean
     */
    public function isTrialSupported();

    /**
     * @return boolean
     */
    public function isInitialAmountSupported();

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
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateAll($data);

    /**
     * @param string $status
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateSubscriptionStatus($status);

    /**
     * @param string $reference
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateSubscriptionReference($reference);

    /**
     * @param string $name
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateSubscriptionName($name);

    /**
     * @param string  $unit
     * @param integer $length
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateInterval($unit, $length);

    /**
     * @param Zend_Date $date
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateStartDate(Zend_Date $date);

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateTotalCycles($cycles);

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateAmount($amount);

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateInitialAmount($amount);

    /**
     * @param string $currencyCode
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateCurrencyCode($currencyCode);

    /**
     * @param integer $cycles
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateTrialCycles($cycles);

    /**
     * @param float $amount
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateTrialAmount($amount);

    /**
     * @param Mage_Payment_Model_Info $paymentInfo
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validatePaymentInfo(Mage_Payment_Model_Info $paymentInfo);

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateCustomerInfo(Mage_Customer_Model_Customer $customer);

    /**
     * @param Mage_Customer_Model_Address_Abstract $billingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateBillingAddress(Mage_Customer_Model_Address_Abstract $billingAddress);

    /**
     * @param Mage_Customer_Model_Address_Abstract $shippingAddress
     *
     * @throws AW_Sarp2_Model_Engine_PaymentRestrictionsException
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function validateShippingAddress(Mage_Customer_Model_Address_Abstract $shippingAddress);
}