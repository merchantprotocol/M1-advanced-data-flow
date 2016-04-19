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

class AW_Sarp2_Test_Model_Engine_Paypal_Restrictions extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @doNotIndexAll
     */
    public function getAvailableSubscriptionStatus()
    {
        $expectations = array('Active', 'Pending', 'Cancelled', 'Suspended', 'Expired');
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->getAvailableSubscriptionStatus();
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @param string $currentStatus
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getAvailableSubscriptionOperations($currentStatus)
    {
        switch ($currentStatus) {
            case 'Active' :
                $expectations = array('update', 'suspend', 'cancel');
                break;
            case 'Suspended' :
                $expectations = array('update', 'activate', 'cancel');
                break;
            case 'Cancelled' :
            case 'Pending' :
            case 'Expired' :
                $expectations = array();
                break;
            default:
                $expectations = array();
                break;
        }
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->getAvailableSubscriptionOperations($currentStatus);
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @test
     * @doNotIndexAll
     */
    public function getAvailableUnitOfTime()
    {
        $expectations = array('Day', 'Week', 'SemiMonth', 'Month', 'Year');
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->getAvailableUnitOfTime();
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @test
     * @doNotIndexAll
     */
    public function getStartDateFormat()
    {
        // time format
        $expectations = 'yyyy-MM-ddThh:mm:ss';
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->getStartDateFormat();
        $this->assertTrue(is_string($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     */
    public function isTrialSupported()
    {
        $expectations = true;
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->isTrialSupported();
        $this->assertTrue(is_bool($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     */
    public function isInitialAmountSupported()
    {
        $expectations = true;
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = $model->isInitialAmountSupported();
        $this->assertTrue(is_bool($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProviderForValidateAll
     */
    public function validateAll($data, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateAll($data);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string $status
     * @param bool   $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateSubscriptionStatus($status, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionStatus($status);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string $reference
     * @param bool   $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateSubscriptionReference($reference, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionReference($reference);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string $name
     * @param bool   $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateSubscriptionName($name, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionName($name);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string  $unit
     * @param integer $length
     * @param bool    $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateInterval($unit, $length, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateInterval($unit, $length);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string $date
     * @param string $timezone
     * @param bool   $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateStartDate($date, $timezone, $expectations)
    {
        $zendDate = $this->_createArgumentForValidateStartDate($date, $timezone);

        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateStartDate($zendDate);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param integer $cycles
     * @param bool    $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateTotalCycles($cycles, $expectations)
    {
        if (substr($cycles, 0, 1) === '-') {
            if (strpos($cycles, '.') !== -1) {
                $cycles = (float)$cycles;
            } else {
                $cycles = (int)$cycles;
            }
        }
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateTotalCycles($cycles);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param float $amount
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param float $amount
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateInitialAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateInitialAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param string $currencyCode
     * @param bool   $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateCurrencyCode($currencyCode, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateCurrencyCode($currencyCode);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param integer $cycles
     * @param bool    $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateTrialCycles($cycles, $expectations)
    {
        if (substr($cycles, 0, 1) == '-') {
            if (strpos($cycles, '.') !== -1) {
                $cycles = (float)$cycles;
            } else {
                $cycles = (int)$cycles;
            }
        }
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateTrialCycles($cycles);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param float $amount
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateTrialAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateTrialAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param array $paymentInfoData
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validatePaymentInfo($paymentInfoData, $expectations)
    {
        $paymentInfo = $this->_createArgumentForValidatePaymentInfo($paymentInfoData);

        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validatePaymentInfo($paymentInfo);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param array $customerInfoData
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateCustomerInfo($customerInfoData, $expectations)
    {
        $customer = $this->_createArgumentForValidateCustomerInfo($customerInfoData);

        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateCustomerInfo($customer);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param array $billingAddressData
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateBillingAddress($billingAddressData, $expectations)
    {
        $billingAddress = $this->_createArgumentForValidateBillingAddress($billingAddressData);

        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateBillingAddress($billingAddress);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @param array $shippingAddressData
     * @param bool  $expectations
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function validateShippingAddress($shippingAddressData, $expectations)
    {
        $shippingAddress = $this->_createArgumentForValidateShippingAddress($shippingAddressData);

        $model = Mage::getModel('aw_sarp2/engine_paypal_restrictions');
        $reality = true;
        try {
            $model->validateShippingAddress($shippingAddress);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    ######################### helpers #####################################

    protected function _createArgumentForValidateCustomerInfo($customerInfo)
    {
        //create mock object
        $customer = $this->getModelMock(
            'customer/customer',
            array('getPrimaryBillingAddress', 'getPrimaryShippingAddress')
        );
        $customer->setData(
            array(
                 'email'        => isset($customerInfo['email']) ? $customerInfo['email'] : null,
                 'firstname'    => isset($customerInfo['firstname']) ? $customerInfo['firstname'] : null,
                 'middlename'   => isset($customerInfo['middlename']) ? $customerInfo['middlename'] : null,
                 'lastname'     => isset($customerInfo['lastname']) ? $customerInfo['lastname'] : null,
                 'prefix'       => isset($customerInfo['prefix']) ? $customerInfo['prefix'] : null,
                 'suffix'       => isset($customerInfo['suffix']) ? $customerInfo['suffix'] : null,
                 'payer_id'     => isset($customerInfo['payer_id']) ? $customerInfo['payer_id'] : null,
                 'payer_status' => isset($customerInfo['payer_status']) ? $customerInfo['payer_status'] : null,
            )
        );
        $address = Mage::getModel('customer/address');
        $address->setCustomer($customer);
        if (isset($customerInfo['id'])) {
            $customer->setData('id', $customerInfo['id']);
        }
        if (isset($customerInfo['telephone'])) {
            $address->setData('telephone', $customerInfo['telephone']);
        }
        if (isset($customerInfo['fax'])) {
            $address->setData('fax', $customerInfo['fax']);
        }
        if (isset($customerInfo['company'])) {
            $address->setData('company', $customerInfo['company']);
        }
        if (isset($customerInfo['prefix'])) {
            $address->setData('prefix', $customerInfo['prefix']);
        }
        $customer->expects($this->any())
            ->method('getPrimaryBillingAddress')
            ->will($this->returnValue($address));
        $customer->expects($this->any())
            ->method('getPrimaryShippingAddress')
            ->will($this->returnValue($address));
        return $customer;
    }

    protected function _createArgumentForValidateStartDate($date, $timezone)
    {
        $timestamp = strtotime($date);
        $zendDate = new Zend_Date($timestamp, null);
        $zendDate->setTimezone($timezone);
        return $zendDate;
    }

    protected function _createArgumentForValidatePaymentInfo($paymentInfo)
    {
        //create mock object
        $payment = new Mage_Payment_Model_Info();
        $payment->setData($paymentInfo);
        return $payment;
    }

    protected function _createArgumentForValidateBillingAddress($addressInfo)
    {
        $address = Mage::getModel('customer/address');
        $address->setData($addressInfo);
        $streets = array();
        if (isset($address['street1'])) {
            $streets[] = $addressInfo['street1'];
        }
        if (isset($addressInfo['street2'])) {
            $streets[] = $addressInfo['street2'];
        }
        if (count($streets)) {
            $address->setData('street', $streets);
        }
        return $address;
    }

    protected function _createArgumentForValidateShippingAddress($addressInfo)
    {
        $address = Mage::getModel('customer/address');
        $address->setData($addressInfo);
        $streets = array();
        if (isset($address['street1'])) {
            $streets[] = $addressInfo['street1'];
        }
        if (isset($addressInfo['street2'])) {
            $streets[] = $addressInfo['street2'];
        }
        if (count($streets)) {
            $address->setData('street', $streets);
        }
        return $address;
    }

    public function dataProviderForValidateAll($testName)
    {
        $tests = array(
            'validateSubscriptionStatus'    => 'subscription_status',
            'validateSubscriptionReference' => 'subscription_reference',
            'validateSubscriptionName'      => 'subscription_name',
            'validateInterval'              => array('interval' => array('unit', 'length')),
            'validateStartDate'             => 'start_date',
            'validateTotalCycles'           => 'total_cycles',
            'validateAmount'                => 'amount',
            'validateInitialAmount'         => 'initial_amount',
            'validateCurrencyCode'          => 'currency_code',
            'validateTrialCycles'           => 'trial_cycles',
            'validateTrialAmount'           => 'trial_amount',
            'validatePaymentInfo'           => 'payment_info',
            'validateCustomerInfo'          => 'customer',
            'validateBillingAddress'        => 'billing_address',
            'validateShippingAddress'       => 'shipping_address',
        );
        $dataForValidateAll = array();
        foreach ($tests as $name => $key) {
            $filePath = $this->getYamlFilePath('providers', $name);
            if (!$filePath) {
                throw new RuntimeException('Unable to load data provider for the current test');
            }
            foreach (Spyc::YAMLLoad($filePath) as $provider) {
                if (method_exists($this, '_createArgumentFor' . ucwords($name))) {
                    $dataForValidateAll[$name][] = array(
                        call_user_func_array(array($this, '_createArgumentFor' . ucwords($name)), $provider),
                        $provider[count($provider) - 1]
                    );
                } else {
                    $dataForValidateAll[$name][] = $provider;
                }
            }
        }
        $providers = array();
        foreach ($dataForValidateAll as $mName => $data) {
            foreach ($data as $k => $value) {
                $keyName = $tests[$mName];
                if (is_array($keyName)) {
                    $index = 0;
                    foreach ($keyName as $itemName => $item) {
                        $providers[$k][0][$itemName] = array();
                        foreach ($item as $subItem) {
                            $providers[$k][0][$itemName][$subItem] = $value[$index];
                            $index++;
                        }
                    }
                } else {
                    $providers[$k][0][$keyName] = $value[0];
                }
                $providers[$k][1] = !isset($providers[$k][1])
                    ? $value[count($value) - 1]
                    : ($providers[$k][1]
                        && $value[count($value) - 1]);
            }
        }
        $providersIndex = count($providers);
        //special select for TRUE result
        foreach ($dataForValidateAll as $mName => $data) {
            foreach ($data as $k => $value) {
                $providersKey = $providersIndex + $k;
                $result = !isset($providers[$providersKey][1]) ? $value[count($value) - 1]
                    : ($providers[$providersKey][1] && $value[count($value) - 1]);
                if (!$result) {
                    break;
                }
                $providers[$providersKey][1] = $result;
                $keyName = $tests[$mName];
                if (is_array($keyName)) {
                    $index = 0;
                    foreach ($keyName as $itemName => $item) {
                        $providers[$providersKey][0][$itemName] = array();
                        foreach ($item as $subItem) {
                            $providers[$providersKey][0][$itemName][$subItem] = $value[$index];
                            $index++;
                        }
                    }
                } else {
                    $providers[$providersKey][0][$keyName] = $value[0];
                }
                ksort($providers[$providersKey]);
            }
        }
        return $providers;
    }
}