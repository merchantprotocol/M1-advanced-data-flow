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


class AW_Sarp2_Model_Engine_Authorizenet_Engine implements AW_Sarp2_Model_Engine_EngineInterface
{
    const INFINITE_TOTAL_CYCLES = 9999;

    /**
     * Engine code
     *
     * @var string
     */
    protected $_engineCode = 'authorizenet';

    /**
     * Payment restrictions for engine
     *
     * @var AW_Sarp2_Model_Engine_Authorizenet_Restrictions
     */
    protected $_paymentRestrictions;

    /**
     * Engine of service
     *
     * @var AW_Sarp2_Model_Engine_Authorizenet_Service
     */
    protected $_service;

    protected $_mapRestrictionsToProfile = array(
        'start_date'                    => 'start_date',
        'status'                        => 'status',
        'name'                          => 'details/order_item_info/name',
        'description'                   => 'details/description',
        'interval/unit'                 => 'details/subscription/type/period_unit',
        'interval/length'               => 'details/subscription/type/period_length',
        'trial_interval/unit'           => 'details/subscription/type/period_unit',
        'trial_interval/length'         => 'details/subscription/type/period_length',
        'amount'                        => 'amount',
        'currency_code'                 => 'details/currency_code',
        'total_cycles'                  => 'details/subscription/type/period_number_of_occurrences',
        'shipping_amount'               => 'details/shipping_amount',
        'tax_amount'                    => 'details/tax_amount',
        'initial_amount'                => 'details/subscription/item/initial_fee_price',
        'trial_amount'                  => 'details/subscription/item/trial_price',
        'trial_cycles'                  => 'details/subscription/type/trial_number_of_occurrences',
        'customer'                      => 'details/customer',
        'billing_address'               => 'details/billing_address',
        'shipping_address'              => 'details/shipping_address',
        'next_billing_date'             => 'details/next_billing_date',
        'final_payments_date'           => 'details/final_payments_date',
        'shipping_address/name'         => 'details/shipping_address/name',
        'shipping_address/street1'      => 'details/shipping_address/street1',
        'shipping_address/street2'      => 'details/shipping_address/street2',
        'shipping_address/city'         => 'details/shipping_address/city',
        'shipping_address/region'       => 'details/shipping_address/region',
        'shipping_address/postcode'     => 'details/shipping_address/postcode',
        'shipping_address/country_id'   => 'details/shipping_address/country_id',
        'shipping_address/telephone'    => 'details/shipping_address/telephone',
        'shipping_address/fax'          => 'details/shipping_address/fax',
        'cardNumber'                    => 'details/payment/cc_number',
        'expirationDate'                => 'details/payment/cc_exp_year',
    );

    protected $_mapRestrictionsToService = array(
        'name'                                      => 'subscription/name',
        'interval/length'                           => 'subscription/paymentSchedule/interval/length',
        'interval/unit'                             => 'subscription/paymentSchedule/interval/unit',
        'start_date'                                => 'subscription/paymentSchedule/startDate',
        'total_cycles'                              => 'subscription/paymentSchedule/totalOccurrences',
        'trial_cycles'                              => 'subscription/paymentSchedule/trialOccurrences',
        'amount'                                    => 'subscription/amount',
        'trial_amount'                              => 'subscription/trialAmount',
        'cardNumber'                                => 'subscription/payment/creditCard/cardNumber',
        'expirationDate'                            => 'subscription/payment/creditCard/expirationDate',
        'description'                               => 'subscription/order/description',
        'customer/email'                            => 'subscription/customer/email',
        //'shipping_address/telephone'                => 'subscription/customer/phoneNumber',
        'shipping_address/fax'                      => 'subscription/customer/faxNumber',
        'customer/firstname'                        => 'subscription/billTo/firstName',
        'customer/lastname'                         => 'subscription/billTo/lastName',
        'customer/primary_billing_address/company'  => 'subscription/billTo/company',
        'billing_address/street1'                   => 'subscription/billTo/address',
        'billing_address/city'                      => 'subscription/billTo/city',
        'billing_address/region'                    => 'subscription/billTo/state',
        'billing_address/postcode'                  => 'subscription/billTo/zip',
        'billing_address/country_id'                => 'subscription/billTo/country',
        'shipping_address/name'                     => 'subscription/shipTo/firstName',
        'shipping_address/street1'                  => 'subscription/shipTo/address',
        'shipping_address/city'                     => 'subscription/shipTo/city',
        'shipping_address/region'                   => 'subscription/shipTo/state',
        'shipping_address/postcode'                 => 'subscription/shipTo/zip',
        'shipping_address/country_id'               => 'subscription/shipTo/country',
    );

    protected $_responseMapRestrictionsToService
        = array(
            'status' => 'status',
        );

    function __construct()
    {
        /*
         * Initialize class parameters
         */
        $this->_paymentRestrictions = Mage::getSingleton('aw_sarp2/engine_authorizenet_restrictions');
        $this->_service = Mage::getSingleton('aw_sarp2/engine_authorizenet_service');
    }

    /**
     * @return string
     */
    public function getEngineCode()
    {
        return $this->_engineCode;
    }

    /**
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function getPaymentRestrictionsModel()
    {
        return $this->_paymentRestrictions;
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     *
     * @throws Mage_Core_Exception
     */
    public function createRecurringProfile(AW_Sarp2_Model_Profile $p)
    {
        $this->_initConfigData($p);
        $data = $this->_importDataForValidation($p);
        try {
            $this->getPaymentRestrictionsModel()->validateAll($data);
        } catch (AW_Sarp2_Model_Engine_PaymentRestrictionsException $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }
        $requestData = $this->_importDataForService($p);
        try {
            $quote = Mage::getModel('sales/quote')->load($p->getData('details/order_item_info/quote_id'));
            if (is_null($quote->getId())) {
                throw new Exception('Unable get quote');
            }
            $response = $this->_service->createSubscription($requestData);
            $details = $p->getData('details');
            unset($details['payment']);
            $p->addData(
                array(
                     'reference_id' => $response['subscriptionId'],
                     'start_date'   => $data['start_date']->toString(Zend_Date::W3C),
                     'details'      => $details,
                )
            );
        } catch (AW_Sarp2_Model_Engine_Authorizenet_Service_Exception $e) {
            Mage::logException($e);
            $message = Mage::helper('aw_sarp2')->__(
                'Unable create subscription on Authorize.net: %s', $e->getMessage()
            );
            throw new Mage_Core_Exception($message);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable create subscription on Authorize.net');
        }
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     */
    public function updateRecurringProfile(AW_Sarp2_Model_Profile $p)
    {
        //TODO: add service && add validation
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     */
    public function updateStatusToActive(AW_Sarp2_Model_Profile $p, $note)
    {

    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     */
    public function updateStatusToSuspended(AW_Sarp2_Model_Profile $p, $note)
    {

    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     *
     * @throws Mage_Core_Exception
     */
    public function updateStatusToCanceled(AW_Sarp2_Model_Profile $p, $note)
    {
        $this->_initConfigData($p);
        try {
            $this->_service->cancelSubscription($p->getReferenceId());
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable change profile status to Cancel');
        }
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     *
     * @throws Mage_Core_Exception
     * @return array
     */
    public function getRecurringProfileDetails(AW_Sarp2_Model_Profile $p)
    {
        $this->_initConfigData($p);
        try {
            $response = $this->_service->getSubscriptionStatus($p->getReferenceId());
            return $this->_importDataFromServiceToProfile($response);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable get profile details from Authorize.net');
        }
    }

    /**
     * @return AW_Sarp2_Model_Engine_Authorizenet_Source_Unit|null
     */
    public function getUnitSource()
    {
        return Mage::getModel('aw_sarp2/engine_authorizenet_source_unit');
    }

    /**
     * @return AW_Sarp2_Model_Engine_Authorizenet_Source_Status
     */
    public function getStatusSource()
    {
        return Mage::getModel('aw_sarp2/engine_authorizenet_source_status');
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     */
    private function _initConfigData(AW_Sarp2_Model_Profile $p)
    {
        $methodInstance = Mage::helper('payment')->getMethodInstance($p->getData('details/method_code'));
        $methodInstance->setStore($p->getData('details/store_id'));
        $this->_service->setConfigData(
            array(
                 'test_mode'      => $methodInstance->getConfigData('test'),
                 'transactionKey' => $methodInstance->getConfigData('trans_key'),
                 'name'           => $methodInstance->getConfigData('login'),
            )
        );
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     *
     * @return array
     */
    protected function _importDataForValidation(AW_Sarp2_Model_Profile $p)
    {
        $data = array();
        foreach ($this->_mapRestrictionsToProfile as $restrictionsKey => $profileKey) {
            $levels = explode('/', $restrictionsKey);
            $currentData = &$data;
            foreach ($levels as $key) {
                if (!isset($currentData[$key])) {
                    $currentData[$key] = array();
                }
                $currentData = &$currentData[$key];
            }
            $currentData = $p->getData($profileKey);
        }
        if (isset($data['shipping_address'])) {
            $address = Mage::getModel('customer/address');
            $address->setData($data['shipping_address']);
            $data['shipping_address'] = $address;
        }
        if (isset($data['billing_address'])) {
            $address = Mage::getModel('customer/address');
            $address->setData($data['billing_address']);
            $data['billing_address'] = $address;
        }
        if (isset($data['payment_info'])) {
            $payment = Mage::getModel('payment/info');
            $payment->setData($data['payment_info']);
            $data['payment_info'] = $payment;
        }
        if (isset($data['customer'])) {
            $customer = Mage::getModel('customer/customer')->load($p->getData('customer_id'));
            if (!$customer->getId()) {
                $customer->setData(
                    array(
                         'email'      => isset($data['billing_address']['email'])
                                 ? $data['billing_address']['email'] : null,
                         'prefix'     => isset($data['billing_address']['prefix'])
                                 ? $data['billing_address']['prefix'] : null,
                         'firstname'  => isset($data['billing_address']['firstname'])
                                 ? $data['billing_address']['firstname'] : null,
                         'middlename' => isset($data['billing_address']['middlename'])
                                 ? $data['billing_address']['middlename'] : null,
                         'lastname'   => isset($data['billing_address']['lastname'])
                                 ? $data['billing_address']['lastname'] : null,
                         'suffix'     => isset($data['billing_address']['suffix'])
                                 ? $data['billing_address']['suffix'] : null,
                         'company'    => isset($data['billing_address']['company'])
                                 ? $data['billing_address']['company'] : null,
                    )
                );
            }
            $data['customer'] = $customer;
        }
        if (array_key_exists('start_date', $data)) {
            $subscription = Mage::getModel('aw_sarp2/subscription')->setData(
                $p->getData('details/subscription/general')
            );
            $data['start_date'] = Mage::helper('aw_sarp2/subscription')->calculateSubscriptionStartDateForSelectedDate(
                $subscription, $data['start_date']
            );
        }
        if (isset($data['total_cycles'])) {
            $data['total_cycles'] = (int)$data['total_cycles'];
            if ($p->getData('details/subscription/type/period_is_infinite')) {
                $data['total_cycles'] = self::INFINITE_TOTAL_CYCLES;
            }
        }
        if (isset($data['initial_amount'])) {
            $data['initial_amount'] = (float)$data['initial_amount'];
            if (!$p->getData('details/subscription/type/initial_fee_is_enabled')) {
                unset($data['initial_amount']);
            }
        }
        if (isset($data['trial_cycles']) && isset($data['trial_amount'])) {
            if (!$p->getData('details/subscription/type/trial_is_enabled')) {
                unset($data['trial_cycles']);
                unset($data['trial_amount']);
            } else {
                $data['trial_amount'] = (float)$data['trial_amount'];
                $data['trial_cycles'] = (int)$data['trial_cycles'];
                if (!$p->getData('details/subscription/type/period_is_infinite')) {
                    $data['total_cycles'] += $data['trial_cycles'];
                }
                if ($data['total_cycles'] > self::INFINITE_TOTAL_CYCLES) {
                    $data['total_cycles'] = self::INFINITE_TOTAL_CYCLES;
                }
            }
        } else {
            unset($data['trial_cycles']);
            unset($data['trial_amount']);
        }
        if (isset($data['expirationDate'])) {
            $data['expirationDate']
                = $p->getData('details/payment/cc_exp_year') . '-' . $p->getData('details/payment/cc_exp_month');
        }
        return $data;
    }

    protected function _importDataForService(AW_Sarp2_Model_Profile $p)
    {
        $restrictionsData = $this->_importDataForValidation($p);
        $data = array();
        foreach ($this->_mapRestrictionsToService as $restrictionsKey => $serviceKey) {
            $levels = explode('/', $restrictionsKey);
            $value = isset($restrictionsData[$levels[0]]) ? $restrictionsData[$levels[0]] : null;
            unset($levels[0]);
            foreach ($levels as $key) {
                if (is_object($value)) {
                    $methodName = "get";
                    foreach (explode('_', $key) as $namePart) {
                        $methodName .= uc_words($namePart);
                    }
                    if (method_exists($value, $methodName)) {
                        $value = call_user_func(array($value, $methodName));
                    } else {
                        $value = $value->getData($key);
                    }
                } elseif (is_array($value)) {
                    $value = $value[$key];
                } else {
                    $value = null;
                }
            }

            if (!is_null($value)) {
                $levels = explode('/', $serviceKey);
                $currentData = &$data;
                foreach ($levels as $key) {
                    if (!isset($currentData[$key])) {
                        $currentData[$key] = array();
                    }
                    $currentData = &$currentData[$key];
                }
                $currentData = $value;
            }
        }

        $data['subscription']['amount'] += $p->getData('details/shipping_amount') + $p->getData('details/tax_amount');

        if (!is_null($data['subscription']['paymentSchedule']['startDate'])) {
            $startDate = $data['subscription']['paymentSchedule']['startDate']
                ->setTimezone('US/Mountain')
                ->toString($this->_paymentRestrictions->getStartDateFormat());
            $data['subscription']['paymentSchedule']['startDate'] = $startDate;
        }
        if (!array_key_exists('trial_amount', $data)) {
            unset($data['trial_amount']);
            unset($data['trial_cycles']);
        }
        return $data;
    }

    protected function _importDataFromServiceToProfile($data)
    {
        $map = $this->_mapRestrictionsToProfile;
        foreach ($map as $restrictionKey => $profileKey) {
            unset($map[$restrictionKey]);
            if (isset($this->_responseMapRestrictionsToService[$restrictionKey])) {
                $map[$this->_responseMapRestrictionsToService[$restrictionKey]] = $profileKey;
            } elseif (isset($this->_mapRestrictionsToService[$restrictionKey])) {
                $map[$this->_mapRestrictionsToService[$restrictionKey]] = $profileKey;
            }
        }
        $profileData = array();
        foreach ($map as $serviceKey => $profileKey) {
            if (!isset($data[$serviceKey])) {
                continue;
            }
            $levels = explode('/', $profileKey);
            $currentData = &$profileData;
            foreach ($levels as $key) {
                if (!isset($currentData[$key])) {
                    $currentData[$key] = array();
                }
                $currentData = &$currentData[$key];
            }
            $currentData = $data[$serviceKey];
        }
        return $profileData;
    }
}