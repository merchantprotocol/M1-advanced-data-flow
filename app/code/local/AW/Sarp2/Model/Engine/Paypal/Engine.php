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


class AW_Sarp2_Model_Engine_Paypal_Engine implements AW_Sarp2_Model_Engine_EngineInterface
{
    /**
     * Engine code
     *
     * @var string
     */
    protected $_engineCode = 'paypal';

    /**
     * Payment restrictions for engine
     *
     * @var AW_Sarp2_Model_Engine_Paypal_Restrictions
     */
    protected $_paymentRestrictions;

    /**
     * Engine of service
     *
     * @var AW_Sarp2_Model_Engine_Paypal_Service
     */
    protected $_service;

    protected $_mapRestrictionsToService = array(
        'start_date'                               => 'PROFILESTARTDATE',
        'status'                                   => 'STATUS',
        'description'                              => 'DESC',
        'interval/unit'                            => 'BILLINGPERIOD',
        'interval/length'                          => 'BILLINGFREQUENCY',
        'trial_interval/unit'                      => 'TRIALBILLINGPERIOD',
        'trial_interval/length'                    => 'TRIALBILLINGFREQUENCY',
        'amount'                                   => 'AMT',
        'currency_code'                            => 'CURRENCYCODE',
        'customer/email'                           => 'EMAIL',
        'billing_address/street1'                  => 'STREET',
        'billing_address/city'                     => 'CITY',
        'billing_address/region'                   => 'STATE',
        'billing_address/country_id'               => 'COUNTRYCODE',
        'billing_address/postcode'                 => 'ZIP',
        'total_cycles'                             => 'TOTALBILLINGCYCLES',
        'shipping_amount'                          => 'SHIPPINGAMT',
        'tax_amount'                               => 'TAXAMT',
        'initial_amount'                           => 'INITAMT',
        'trial_amount'                             => 'TRIALAMT',
        'trial_shipping_amount'                    => 'TRIALSHIPPINGAMT',
        'trial_tax_amount'                         => 'TRIALTAXAMT',
        'trial_cycles'                             => 'TRIALTOTALBILLINGCYCLES',
        'shipping_address/name'                    => 'SHIPTONAME',
        'shipping_address/street1'                 => 'SHIPTOSTREET',
        'shipping_address/street2'                 => 'SHIPTOSTREET2',
        'shipping_address/city'                    => 'SHIPTOCITY',
        'shipping_address/region'                  => 'SHIPTOSTATE',
        'shipping_address/postcode'                => 'SHIPTOZIP',
        'shipping_address/country_id'              => 'SHIPTOCOUNTRY',
        'shipping_address/telephone'               => 'SHIPTOPHONENUM',
        'customer/primary_billing_address/company' => 'BUSINESS',
        'customer/prefix'                          => 'SALUTATION',
        'customer/firstname'                       => 'FIRSTNAME',
        'customer/middlename'                      => 'MIDDLENAME',
        'customer/lastname'                        => 'LASTNAME',
        'customer/suffix'                          => 'SUFFIX',
        'billing_address/street2'                  => 'STREET2',
        'billing_address/telephone'                => 'PHONENUM',
    );

    protected $_mapRestrictionsToProfile = array(
        'start_date'                  => 'start_date',
        'status'                      => 'status',
        'description'                 => 'details/description',
        'interval/unit'               => 'details/subscription/type/period_unit',
        'interval/length'             => 'details/subscription/type/period_length',
        'trial_interval/unit'         => 'details/subscription/type/period_unit',
        'trial_interval/length'       => 'details/subscription/type/period_length',
        'amount'                      => 'amount',
        'currency_code'               => 'details/currency_code',
        'total_cycles'                => 'details/subscription/type/period_number_of_occurrences',
        'shipping_amount'             => 'details/shipping_amount',
        'tax_amount'                  => 'details/tax_amount',
        'initial_amount'              => 'details/subscription/item/initial_fee_price',
        'trial_amount'                => 'details/subscription/item/trial_price',
        'trial_shipping_amount'       => 'details/shipping_amount',
        'trial_tax_amount'            => 'details/tax_amount',
        'trial_cycles'                => 'details/subscription/type/trial_number_of_occurrences',
        'customer'                    => 'details/customer',
        'billing_address'             => 'details/billing_address',
        'shipping_address'            => 'details/shipping_address',
        'next_billing_date'           => 'details/next_billing_date',
        'final_payments_date'         => 'details/final_payments_date',
        'shipping_address/name'       => 'details/shipping_address/name',
        'shipping_address/street1'    => 'details/shipping_address/street1',
        'shipping_address/street2'    => 'details/shipping_address/street2',
        'shipping_address/city'       => 'details/shipping_address/city',
        'shipping_address/region'     => 'details/shipping_address/region',
        'shipping_address/postcode'   => 'details/shipping_address/postcode',
        'shipping_address/country_id' => 'details/shipping_address/country_id',
        'shipping_address/telephone'  => 'details/shipping_address/telephone',
    );

    protected $_responseMapRestrictionsToService = array(
        'shipping_address/country_id' => 'SHIPTOCOUNTRYCODE',
        'next_billing_date'           => 'NEXTBILLINGDATE',
        'final_payments_date'         => 'FINALPAYMENTDUEDATE',
        'amount'                      => 'REGULARAMT',
        'shipping_amount'             => 'REGULARSHIPPINGAMT',
        'tax_amount'                  => 'REGULARTAXAMT',
    );


    function __construct()
    {
        /*
         * Initialize class parameters
         */
        $this->_paymentRestrictions = Mage::getSingleton('aw_sarp2/engine_paypal_restrictions');
        $this->_service = Mage::getSingleton('aw_sarp2/engine_paypal_service');
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
            $token = $quote->getPayment()
                ->getAdditionalInformation(
                    Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN
                )
            ;
            $response = $this->_service->createRecurringPaymentsProfile($token, $requestData);
            $status = ($response['PROFILESTATUS'] == 'ActiveProfile') ? 'Active' : 'Pending';
            $p->addData(
                array(
                     'reference_id' => $response['PROFILEID'],
                     'status'       => $status,
                )
            );
        } catch (AW_Sarp2_Model_Engine_Paypal_Service_Exception $e) {
            Mage::logException($e);
            $message = Mage::helper('aw_sarp2')->__('Unable create subscription on PayPal: %s', $e->getMessage());
            throw new Mage_Core_Exception($message);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable create subscription on PayPal');
        }
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     */
    public function updateRecurringProfile(AW_Sarp2_Model_Profile $p)
    {

    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     *
     * @throws Mage_Core_Exception
     */
    public function updateStatusToActive(AW_Sarp2_Model_Profile $p, $note)
    {
        $this->_initConfigData($p);
        try {
            $this->_service->manageRecurringPaymentsProfileStatus($p->getReferenceId(), 'Reactivate', $note);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable change profile status to Active');
        }
    }

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     *
     * @throws Mage_Core_Exception
     */
    public function updateStatusToSuspended(AW_Sarp2_Model_Profile $p, $note)
    {
        $this->_initConfigData($p);
        try {
            $this->_service->manageRecurringPaymentsProfileStatus($p->getReferenceId(), 'Suspend', $note);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable change profile status to Suspend');
        }
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
            $this->_service->manageRecurringPaymentsProfileStatus($p->getReferenceId(), 'Cancel', $note);
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
            $response = $this->_service->getRecurringPaymentsProfileDetails($p->getReferenceId());
            return $this->_importDataFromServiceToProfile($response);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception('Unable get profile details from PayPal');
        }
    }

    /**
     * @return AW_Sarp2_Model_Engine_Paypal_Source_Unit|null
     */
    public function getUnitSource()
    {
        return Mage::getModel('aw_sarp2/engine_paypal_source_unit');
    }

    /**
     * @return AW_Sarp2_Model_Engine_Paypal_Source_Status
     */
    public function getStatusSource()
    {
        return Mage::getModel('aw_sarp2/engine_paypal_source_status');
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
                $data['total_cycles'] = 0;
            }
        }
        if (isset($data['initial_amount'])) {
            $data['initial_amount'] = (float)$data['initial_amount'];
            if (!$p->getData('details/subscription/type/initial_fee_is_enabled')) {
                unset($data['initial_amount']);
            }
        }
        if (isset($data['trial_cycles'])) {
            $data['trial_cycles'] = (int)$data['trial_cycles'];
            if (!$p->getData('details/subscription/type/trial_is_enabled')) {
                unset($data['trial_cycles']);
            }
        }
        if (isset($data['trial_amount'])) {
            $data['trial_amount'] = (float)$data['trial_amount'];
            if (!$p->getData('details/subscription/type/trial_is_enabled')) {
                unset($data['trial_amount']);
            }
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
                $data[$serviceKey] = $value;
            }
        }
        if (!is_null($data['PROFILESTARTDATE'])) {
            $startDate = $data['PROFILESTARTDATE']
                ->setTimezone('UTC')
                ->toString($this->_paymentRestrictions->getStartDateFormat());
            $data['PROFILESTARTDATE'] = $startDate;
        }
        if (!array_key_exists('TRIALAMT', $data)) {
            unset($data['TRIALAMT']);
            unset($data['TRIALTOTALBILLINGCYCLES']);
            unset($data['TRIALBILLINGPERIOD']);
            unset($data['TRIALBILLINGFREQUENCY']);
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
            $currentData = & $profileData;
            foreach ($levels as $key) {
                if (!isset($currentData[$key])) {
                    $currentData[$key] = array();
                }
                $currentData = & $currentData[$key];
            }
            $currentData = $data[$serviceKey];
        }
        unset($profileData['details']['subscription']);
        if (isset($profileData['start_date'])) {
            $zDate = new Zend_Date($profileData['start_date'], $this->_paymentRestrictions->getStartDateFormat());
            $profileData['start_date'] = $zDate->toString(Zend_Date::W3C);
        }
        return $profileData;
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
                 'sandbox_flag'       => $methodInstance->getConfigData('sandbox_flag'),
                 'api_authentication' => $methodInstance->getConfigData('api_authentication'),
                 'username'           => $methodInstance->getConfigData('api_username'),
                 'password'           => $methodInstance->getConfigData('api_password'),
                 'signature'          => $methodInstance->getConfigData('api_signature'),
            )
        );
    }
}