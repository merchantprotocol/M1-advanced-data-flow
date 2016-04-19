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


class AW_Sarp2_Model_Engine_Authorizenet_Service
{
    const API_URL           = 'https://PayTrace.com/API/gateway.pay';
    const API_URL_TEST_MODE = 'https://PayTrace.com/API/gateway.pay';

    const DO_CREATE_SUBSCRIPTION     = 'ARBCreateSubscriptionRequest';
    const DO_UPDATE_SUBSCRIPTION     = 'ARBUpdateSubscriptionRequest';
    const DO_CANCEL_SUBSCRIPTION     = 'ARBCancelSubscriptionRequest';
    const DO_GET_SUBSCRIPTION_STATUS = 'ARBGetSubscriptionStatusRequest';

    protected $_serviceRequestEachCallRequiredFields = array();

    protected $_serviceResponseEachCallFields = array(
        'refId',
        'messages/resultCode',
        'messages/code',
        'messages/text',
    );

    protected $_serviceRequestRequiredFieldsMap = array(
        'ARBCreateSubscriptionRequest' => array(
            'subscription/paymentSchedule/interval/length',
            'subscription/paymentSchedule/interval/unit',
            'subscription/paymentSchedule/startDate',
            'subscription/paymentSchedule/totalOccurrences',
            'subscription/amount',
            'subscription/payment/creditCard/cardNumber',
            'subscription/payment/creditCard/expirationDate',
            'subscription/billTo/firstName',
            'subscription/billTo/lastName',
        ),
        'ARBUpdateSubscriptionRequest' => array(
            'subscriptionId',
            'subscription/amount',
            'subscription/payment/creditCard/cardNumber',
            'subscription/payment/creditCard/expirationDate',
            'subscription/billTo/firstName',
            'subscription/billTo/lastName',
        ),
        'ARBCancelSubscriptionRequest' => array(
            'subscriptionId',
        ),
        'ARBGetSubscriptionStatusRequest' => array(
            'subscriptionId',
        ),
    );

    protected $_serviceRequestOptionalFieldsMap = array(
        'ARBCreateSubscriptionRequest'    => array(
            'refId',
            'subscription/name',
            'subscription/paymentSchedule/trialOccurrences',
            'subscription/trialAmount',
            'subscription/payment/creditCard/cardCode',
            'subscription/order/invoiceNumber',
            'subscription/order/description',
            'subscription/customer/id',
            'subscription/customer/email',
            'subscription/customer/phoneNumber',
            'subscription/customer/faxNumber',
            'subscription/billTo/company',
            'subscription/billTo/address',
            'subscription/billTo/city',
            'subscription/billTo/state',
            'subscription/billTo/zip',
            'subscription/billTo/country',
            'subscription/shipTo/firstName',
            'subscription/shipTo/lastName',
            'subscription/shipTo/company',
            'subscription/shipTo/address',
            'subscription/shipTo/city',
            'subscription/shipTo/state',
            'subscription/shipTo/zip',
            'subscription/shipTo/country',
        ),
        'ARBUpdateSubscriptionRequest'    => array(
            'refId',
            'subscription/name',
            'subscription/paymentSchedule/interval/length',
            'subscription/paymentSchedule/interval/unit',
            'subscription/paymentSchedule/startDate',
            'subscription/paymentSchedule/totalOccurrences',
            'subscription/paymentSchedule/trialOccurrences',
            'subscription/trialAmount',
            'subscription/payment/creditCard/cardCode',
            'subscription/order/invoiceNumber',
            'subscription/order/description',
            'subscription/customer/id',
            'subscription/customer/email',
            'subscription/customer/phoneNumber',
            'subscription/customer/faxNumber',
            'subscription/billTo/company',
            'subscription/billTo/address',
            'subscription/billTo/city',
            'subscription/billTo/state',
            'subscription/billTo/zip',
            'subscription/billTo/country',
            'subscription/shipTo/firstName',
            'subscription/shipTo/lastName',
            'subscription/shipTo/company',
            'subscription/shipTo/address',
            'subscription/shipTo/city',
            'subscription/shipTo/state',
            'subscription/shipTo/zip',
            'subscription/shipTo/country',
        ),
        'ARBCancelSubscriptionRequest'    => array(
            'refId',
        ),
        'ARBGetSubscriptionStatusRequest' => array(
            'refId',
        ),
    );

    protected $_serviceResponseFieldsMap = array(
        'ARBCreateSubscriptionRequest'    => array(
            'subscriptionId',
        ),
        'ARBUpdateSubscriptionRequest'    => array(

        ),
        'ARBCancelSubscriptionRequest'    => array(

        ),
        'ARBGetSubscriptionStatusRequest' => array(
            'status',
        ),
    );

    protected $_client;
    protected $_config = null;
    protected $_callWarnings = array();

    function __construct()
    {
        $this->_client = new Varien_Object();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $this->_client->setHandler($ch);
    }

    /**
     * @param array $data
     */
    public function setConfigData($data)
    {
        $this->_config = $data;
    }

    public function createSubscription($data)
    {
        $this->_setRequestData($data);
        return $this->_runRequest(self::DO_CREATE_SUBSCRIPTION);
    }

    public function updateSubscription($subscriptionId, $data)
    {
        $this->_setRequestData(
            array_merge(
                $data,
                array(
                     'subscriptionId' => $subscriptionId,
                )
            )
        );
        $this->_setRequestData($data);
        return $this->_runRequest(self::DO_UPDATE_SUBSCRIPTION);
    }

    public function cancelSubscription($subscriptionId)
    {
        $this->_setRequestData(array('subscriptionId' => $subscriptionId));
        return $this->_runRequest(self::DO_CANCEL_SUBSCRIPTION);
    }

    public function getSubscriptionStatus($subscriptionId)
    {
        $this->_setRequestData(array('subscriptionId' => $subscriptionId));
        return $this->_runRequest(self::DO_GET_SUBSCRIPTION_STATUS);
    }

    protected function _setRequestData($data)
    {
        $this->_client->setData('request', $data);
    }

    protected function _runRequest($command)
    {
        if (!isset($this->_config['test_mode'])) {
            throw new Exception('Some config fields are not specified');
        }
        $url = !$this->_config['test_mode'] ? self::API_URL : self::API_URL_TEST_MODE;
        curl_setopt($this->_client->getHandler(), CURLOPT_URL, $url);

        if (!isset($this->_config['name']) || !isset($this->_config['transactionKey'])) {
            throw new Exception('Some authenticate config fields are not specified');
        }

        if (
            !isset($this->_serviceRequestRequiredFieldsMap[$command])
            || !isset($this->_serviceRequestOptionalFieldsMap[$command])
            || !isset($this->_serviceResponseFieldsMap[$command])
        ) {
            throw new Exception('Service map does not specified');
        }

        $requestData = $this->_client->getData('request');
        if (is_null($requestData)) {
            throw new Exception('Request data not specified');
        }
        $requiredFields = array();
        foreach ($this->_serviceRequestRequiredFieldsMap[$command] as $value) {
            if (!is_null($this->_client->getData('request/' . $value))) {
                $requiredFields[] = $value;
            }
        }

        $notSpecifiedRequiredFields = array_merge(
            array_diff_key(array_flip($this->_serviceRequestRequiredFieldsMap[$command]), array_flip($requiredFields)),
            array_diff_key(array_flip($this->_serviceRequestEachCallRequiredFields), $requiredFields)
        );

        if (count($notSpecifiedRequiredFields) > 0) {
            $keysAsString = implode(', ', array_keys($notSpecifiedRequiredFields));
            throw new Exception('Some required fields a not specified: ' . $keysAsString);
        }

        $notSpecifiedFields = array_diff_key(
            array_flip($requiredFields),
            array_flip($this->_serviceRequestEachCallRequiredFields),
            array_flip($this->_serviceRequestRequiredFieldsMap[$command]),
            array_flip($this->_serviceRequestOptionalFieldsMap[$command])
        );
        if (count($notSpecifiedFields) > 0) {
            $keysAsString = implode(', ', array_keys($notSpecifiedFields));
            throw new Exception('Some fields a not specified in service map: ' . $keysAsString);
        }


        $requestXml = $this->_buildRequest($command);
        curl_setopt($this->_client->getHandler(), CURLOPT_POSTFIELDS, $requestXml);
        $httpResponse = curl_exec($this->_client->getHandler());
        if (!$httpResponse) {
            $errorString = "Request failed: "
                . curl_error($this->_client->getHandler())
                . " (" . curl_errno($this->_client->getHandler()) . ")";
            throw new Exception($errorString);
        }
        $response = $this->_parseResponse($httpResponse);

        if (!$this->_isCallSuccessful($response)) {
            /**
             * @throw exception
             */
            $this->_handleCallErrors($response);
        }

        $approveResponse = array_merge(
            array_intersect_key($response, array_flip($this->_serviceResponseFieldsMap[$command])),
            array_intersect_key($response, array_flip($this->_serviceResponseEachCallFields))
        );
        return $approveResponse;
    }

    /**
     * @param string $httpResponse
     *
     * @return array
     */
    private function _parseResponse($httpResponse)
    {
        // convert xml to array
        $httpResponse = preg_replace('/ xmlns:xsi[^>]+/', '', $httpResponse);
        $simpleXmlResponse = new Varien_Simplexml_Element($httpResponse);
        return $simpleXmlResponse->asArray();
    }

    private function _isCallSuccessful($response)
    {
        if (!isset($response['messages']['resultCode'])) {
            return false;
        }

        $resultCode = $response['messages']['resultCode'];
        $this->_callWarnings = array();
        if ($resultCode == 'Ok') {
            return true;
        }
        return false;
    }

    private function _handleCallErrors($response)
    {
        $error = sprintf('%s: %s', $response['messages']['message']['code'], $response['messages']['message']['text']);
        if ($error) {
            throw new AW_Sarp2_Model_Engine_Authorizenet_Service_Exception($error);
        }
    }

    protected function _buildRequest($command)
    {
        $xml
            = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<' . $command . ' xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">' .
            '<merchantAuthentication>' .
            '<name>' . $this->_config['name'] . '</name>' .
            '<transactionKey>' . $this->_config['transactionKey'] . '</transactionKey>' .
            '</merchantAuthentication>' .
            $this->_toXml(null, $this->_client->getData('request')) .
            '</' . $command . '>';
        return $xml;
    }

    protected function _toXml($fieldName, $fieldValue)
    {
        $xml = '';
        if ($fieldName) {
            $xml .= "<{$fieldName}>";
        }
        if (is_array($fieldValue)) {
            foreach ($fieldValue as $key => $value) {
                $xml .= $this->_toXml($key, $value);
            }
        } else {
            $xml .= $fieldValue;
        }
        if ($fieldName) {
            $xml .= "</{$fieldName}>";
        }
        return $xml;
    }
}