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


class AW_Sarp2_Test_Model_Engine_Authorizenet_Restrictions extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @doNotIndexAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:26
     */
    public function getAvailableSubscriptionStatus()
    {
        $expectations = array('active', 'expired', 'suspended', 'canceled', 'terminated');
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->getAvailableSubscriptionStatus();
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:30
     */
    public function getAvailableSubscriptionOperations($currentStatus)
    {
        switch ($currentStatus) {
            case 'active':
            case 'suspended':
                $expectations = array('update', 'cancel');
                break;
            default:
                $expectations = array();
        }
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->getAvailableSubscriptionOperations($currentStatus);
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @test
     * @doNotIndexAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:12
     * Format: days, months
     */
    public function getAvailableUnitOfTime()
    {
        $expectations = array('days', 'months');
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->getAvailableUnitOfTime();
        $this->assertTrue(is_array($reality));
        $this->assertEmpty(array_diff($expectations, $reality));
    }

    /**
     * @test
     * @doNotIndexAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:12
     * Format: YYYY-MM-DD
     */
    public function getStartDateFormat()
    {
        //ISO format for Zend_Date
        $expectations = "yyyy-MM-dd";
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->getStartDateFormat();
        $this->assertTrue(is_string($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:13
     */
    public function isTrialSupported()
    {
        $expectations = true;
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->isTrialSupported();
        $this->assertTrue(is_bool($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf
     */
    public function isInitialAmountSupported()
    {
        $expectations = false;
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = $model->isInitialAmountSupported();
        $this->assertTrue(is_bool($reality));
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProviderForValidateAll
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf
     */
    public function validateAll($data, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateAll($data);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::26
     */
    public function validateSubscriptionStatus($status, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionStatus($status);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::12
     * Format: Up to 20 characters
     */
    public function validateSubscriptionReference($reference, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionReference($reference);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::12
     * Format: Up to 50 characters
     */
    public function validateSubscriptionName($name, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateSubscriptionName($name);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::12
     * [unit]Format: days, months
     * [length]Format: Up to 3 digits
     * [length]Notes: If the Interval Unit is "months," can be any number between one (1) and 12.
     * If the Interval Unit is "days," can be any number between seven (7) and 365.
     */
    public function validateInterval($unit, $length, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateInterval($unit, $length);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::12
     * Notes: The date entered must be greater than or equal to the date the subscription
     * was created.
     * The validation checks against local server date, which is Mountain Time. An error
     * might possibly occur if you try to submit a subscription from a time zone where the
     * resulting date is different; for example, if you are in the Pacific time zone and try to
     * submit a subscription between 11:00 PM and midnight, with a start date set for today.
     */
    public function validateStartDate($date, $timezone, $expectations)
    {
        $zDate = $this->_createArgumentForValidateStartDate($date, $timezone);
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateStartDate($zDate);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::12
     * Notes: To submit a subscription with no end date (an ongoing subscription), this field
     * must be submitted with a value of ???9999.???
     * If a trial period is specified, this number should include the Trial Occurrences.
     */
    public function validateTotalCycles($cycles, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateTotalCycles($cycles);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page::13
     * Format: Up to 15 digits
     */
    public function validateAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf
     */
    public function validateInitialAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateInitialAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf
     */
    public function validateCurrencyCode($currencyCode, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateCurrencyCode($currencyCode);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:13 && E00024
     * Format: Up to 2 digits
     */
    public function validateTrialCycles($cycles, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateTrialCycles($cycles);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:13
     * Format: Up to 15 digits
     * Notes: Required when trial occurrences is specified
     */
    public function validateTrialAmount($amount, $expectations)
    {
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateTrialAmount($amount);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }


    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:13
     * [cardNumber]Format: 13 to 16 digits
     * [expirationDate]Format: YYYY-MM
     * [cardCode]Format: 3 or 4 digits
     */
    public function validatePaymentInfo($paymentInfo, $expectations)
    {
        $payment = $this->_createArgumentForValidatePaymentInfo($paymentInfo);
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validatePaymentInfo($payment);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:14
     * [id]Required: no; Format: Up to 20 characters
     * [email]Required: yea; Format: Up to 255 characters
     * [phoneNumber]Required: no; Format: Up to 25 digits
     * [faxNumber]Required: no; Format: Up to 25 digits
     */
    public function validateCustomerInfo($customerInfo, $expectations)
    {
        $customer = $this->_createArgumentForValidateCustomerInfo($customerInfo);
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateCustomerInfo($customer);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:14-15
     * [firstName]Format: Up to 50 characters
     * [lastName]Format: Up to 50 characters
     * [company]Format: Up to 50 characters
     * [address]Format: Up to 60 characters
     * [city]Format: Up to 40 characters
     * [state]Format: 2 characters
     * [zip]Format: Up to 20 characters
     * [country]Format: Up to 60 characters
     */
    public function validateBillingAddress($addressInfo, $expectations)
    {
        $address = $this->_createArgumentForValidateBillingAddress($addressInfo);
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateBillingAddress($address);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    /**
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     * @infoSource http://www.authorize.net/support/ARB_guide.pdf page:15-16
     * [firstName]Format: Up to 50 characters
     * [lastName]Format: Up to 50 characters
     * [company]Format: Up to 50 characters
     * [address]Format: Up to 60 characters
     * [city]Format: Up to 40 characters
     * [state]Format: 2 characters
     * [zip]Format: Up to 20 characters
     * [country]Format: Up to 60 characters
     */
    public function validateShippingAddress($addressInfo, $expectations)
    {
        $address = $this->_createArgumentForValidateShippingAddress($addressInfo);
        $model = Mage::getModel('aw_sarp2/engine_authorizenet_restrictions');
        $reality = true;
        try {
            $model->validateShippingAddress($address);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            $reality = false;
        }
        $this->assertEquals($expectations, $reality);
    }

    ######################### helpers #####################################

    protected function _createArgumentForValidateStartDate($date, $timezone)
    {
        $now = new Zend_Date();
        $now->setTimezone($timezone);
        $timestamp = strtotime($date) + $now->getGmtOffset();
        $zDate = new Zend_Date($timestamp, null);
        $zDate->setTimezone($timezone);
        return $zDate;
    }

    protected function _createArgumentForValidatePaymentInfo($paymentInfo)
    {
        //create mock object
        $payment = new Mage_Payment_Model_Info();
        $payment->setData(
            array(
                 'cc_number'    => $paymentInfo['cc_number'],
                 'cc_cid'       => $paymentInfo['cc_cid'],
                 'cc_exp_month' => $paymentInfo['cc_exp_month'],
                 'cc_exp_year'  => $paymentInfo['cc_exp_year'],
                 'cc_type'      => $paymentInfo['cc_type'],
            )
        );
        return $payment;
    }

    protected function _createArgumentForValidateCustomerInfo($customerInfo)
    {
        //create mock object
        $customer = $this->getModelMock(
            'customer/customer',
            array('getPrimaryBillingAddress', 'getPrimaryShippingAddress')
        );
        $customer->setData(
            array(
                 'email'     => isset($customerInfo['email']) ? $customerInfo['email'] : null,
                 'firstname' => isset($customerInfo['firstname']) ? $customerInfo['firstname'] : null,
                 'lastname'  => isset($customerInfo['lastname']) ? $customerInfo['lastname'] : null,
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
        $customer->expects($this->any())
            ->method('getPrimaryBillingAddress')
            ->will($this->returnValue($address));
        $customer->expects($this->any())
            ->method('getPrimaryShippingAddress')
            ->will($this->returnValue($address));
        return $customer;
    }

    protected function _createArgumentForValidateBillingAddress($addressInfo)
    {
        $address = Mage::getModel('customer/address');
        $address->setData(
            array(
                 'firstname' => $addressInfo['firstname'],
                 'lastname'  => $addressInfo['lastname'],
                 'company'   => $addressInfo['company'],
                 'street1'   => $addressInfo['street1'],
                 'city'      => $addressInfo['city'],
                 'region_id' => $addressInfo['region'],
                 'postcode'  => $addressInfo['postcode'],
                 'country'   => $addressInfo['country'],
            )
        );
        return $address;
    }

    protected function _createArgumentForValidateShippingAddress($addressInfo)
    {
        $address = Mage::getModel('customer/address');
        $address->setData(
            array(
                 'firstname' => $addressInfo['firstname'],
                 'lastname'  => $addressInfo['lastname'],
                 'company'   => $addressInfo['company'],
                 'street1'   => $addressInfo['street1'],
                 'city'      => $addressInfo['city'],
                 'region_id' => $addressInfo['region'],
                 'postcode'  => $addressInfo['postcode'],
                 'country'   => $addressInfo['country'],
            )
        );
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