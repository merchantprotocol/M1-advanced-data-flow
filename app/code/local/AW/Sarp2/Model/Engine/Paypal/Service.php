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


class AW_Sarp2_Model_Engine_Paypal_Service
{
    const DO_CREATE_RECURRING_PAYMENTS_PROFILE        = 'CreateRecurringPaymentsProfile';
    const DO_GET_RECURRING_PAYMENTS_PROFILE_DETAILS   = 'GetRecurringPaymentsProfileDetails';
    const DO_MANAGE_RECURRING_PAYMENTS_PROFILE_STATUS = 'ManageRecurringPaymentsProfileStatus';
    const DO_UPDATE_RECURRING_PAYMENTS_PROFILE        = 'UpdateRecurringPaymentsProfile';
    const DO_BILL_OUTSTANDING_AMOUNT                  = 'BillOutstandingAmount';

    protected $_serviceRequestEachCallRequiredFields = array(
        'VERSION', 'USER', 'PWD', 'SIGNATURE', 'METHOD',
    );

    protected $_serviceResponseEachCallFields = array(
        'ACK', 'CORRELATIONID', 'TIMESTAMP', 'VERSION', 'BUILD',
    );

    protected $_serviceRequestRequiredFieldsMap = array(
        'CreateRecurringPaymentsProfile'       => array(
            'TOKEN', 'PROFILESTARTDATE', 'DESC', 'BILLINGPERIOD', 'BILLINGFREQUENCY', 'AMT', 'CURRENCYCODE',
            'EMAIL', 'STREET', 'CITY', 'STATE', 'COUNTRYCODE', 'ZIP',
        ),
        'GetRecurringPaymentsProfileDetails'   => array(
            'PROFILEID',
        ),
        'ManageRecurringPaymentsProfileStatus' => array(
            'PROFILEID', 'ACTION',
        ),
        'UpdateRecurringPaymentsProfile'       => array(
            'PROFILEID', 'AMT', 'TRIALAMT', 'CURRENCYCODE', 'STREET', 'CITY', 'STATE', 'COUNTRYCODE', 'ZIP',
        ),
        'BillOutstandingAmount'                => array(
            'PROFILEID',
        )
    );

    protected $_serviceRequestOptionalFieldsMap = array(
        'CreateRecurringPaymentsProfile'       => array(
            'SUBSCRIBERNAME', 'PROFILEREFERENCE', 'MAXFAILEDPAYMENTS', 'AUTOBILLOUTAMT', 'TOTALBILLINGCYCLES',
            'TRIALBILLINGPERIOD', 'TRIALBILLINGFREQUENCY', 'TRIALTOTALBILLINGCYCLES', 'SHIPPINGAMT', 'TAXAMT',
            'INITAMT', 'TRIALAMT', 'TRIALSHIPPINGAMT', 'TRIALTAXAMT', 'FAILEDINITAMTACTION', 'SHIPTONAME',
            'SHIPTOSTREET', 'SHIPTOSTREET2', 'SHIPTOCITY', 'SHIPTOSTATE', 'SHIPTOZIP', 'SHIPTOCOUNTRY',
            'SHIPTOPHONENUM', 'PAYERID', 'PAYERSTATUS', 'BUSINESS', 'SALUTATION', 'FIRSTNAME', 'MIDDLENAME', 'LASTNAME',
            'SUFFIX', 'STREET2', 'PHONENUM',
        ),
        'GetRecurringPaymentsProfileDetails'   => array(),
        'ManageRecurringPaymentsProfileStatus' => array(
            'NOTE'
        ),
        'UpdateRecurringPaymentsProfile'       => array(
            'NOTE', 'DESC', 'SUBSCRIBERNAME', 'PROFILEREFERENCE', 'ADDITIONALBILLINGCYCLES', 'AMT', 'SHIPPINGAMT',
            'TAXAMT', 'OUTSTANDINGAMT', 'AUTOBILLOUTAMT', 'MAXFAILEDPAYMENTS', 'PROFILESTARTDATE', 'SHIPTONAME',
            'SHIPTOSTREET', 'SHIPTOSTREET2', 'SHIPTOCITY', 'SHIPTOSTATE', 'SHIPTOZIP', 'SHIPTOCOUNTRY',
            'SHIPTOPHONENUM', 'TOTALBILLINGCYCLES', 'TRIALTOTALBILLINGCYCLES', 'SHIPPINGAMT', 'TAXAMT', 'EMAIL',
            'STREET2', 'PHONENUM', 'FIRSTNAME', 'LASTNAME',
        ),
        'BillOutstandingAmount'                => array(
            'AMT', 'NOTE'
        )
    );

    protected $_serviceResponseFieldsMap = array(
        'CreateRecurringPaymentsProfile'       => array(
            'PROFILEID', 'PROFILESTATUS'
        ),
        'GetRecurringPaymentsProfileDetails'   => array(
            'PROFILEID', 'STATUS', 'DESC', 'AUTOBILLOUTAMT', 'MAXFAILEDPAYMENTS', 'AGGREGATEAMOUNT',
            'AGGREGATEOPTIONALAMOUNT', 'FINALPAYMENTDUEDATE', 'SUBSCRIBERNAME', 'PROFILESTARTDATE', 'PROFILEREFERENCE',
            'ADDRESSSTATUS', 'SHIPTONAME', 'SHIPTOSTREET', 'SHIPTOSTREET2', 'SHIPTOCITY', 'SHIPTOSTATE', 'SHIPTOZIP',
            'SHIPTOCOUNTRYCODE', 'BILLINGPERIOD', 'REGULARBILLINGPERIOD', 'BILLINGFREQUENCY', 'REGULARBILLINGFREQUENCY',
            'TOTALBILLINGCYCLES', 'REGULARTOTALBILLINGCYCLES', 'AMT', 'REGULARAMT', 'SHIPPINGAMT', 'REGULARSHIPPINGAMT',
            'TAXAMT', 'REGULARTAXAMT', 'CURRENCYCODE', 'REGULARCURRENCYCODE', 'NEXTBILLINGDATE', 'NUMCYLESCOMPLETED',
            'NUMCYCLESREMAINING', 'OUTSTANDINGBALANCE', 'FAILEDPAYMENTCOUNT', 'LASTPAYMENTDATE', 'LASTPAYMENTAMT',
            'CREDITCARDTYPE', 'ACCT', 'EXPDATE', 'STARTDATE', 'ISSUENUMBER', 'EMAIL', 'FIRSTNAME', 'LASTNAME',
            'ADDRESSOWNER', 'ADDRESSSTATUS', 'SECONDARYNAME', 'NAME', 'STREET', 'SECONDARYADDRESSLINE1', 'STREET2',
            'SECONDARYADDRESSLINE2', 'CITY', 'SECONDARYCITY', 'STATE', 'SECONDARYSTATE', 'ZIP', 'SECONDARYZIP',
            'COUNTRYCODE', 'SECONDARYCOUNTRYCODE', 'PHONENUM', 'SECONDARYPHONENUM',
        ),
        'ManageRecurringPaymentsProfileStatus' => array(
            'PROFILEID'
        ),
        'UpdateRecurringPaymentsProfile'       => array(
            'PROFILEID'
        ),
        'BillOutstandingAmount'                => array(
            'PROFILEID'
        )
    );

    protected $_client;
    protected $_config = null;
    protected $_callWarnings = array();
    protected $_callErrors = array();

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
        $this->_client->setHandler($ch);
    }

    public function createRecurringPaymentsProfile($token, $data)
    {
        $this->_setRequestData(
            array_merge(
                $data,
                array(
                     'TOKEN' => $token
                )
            )
        );
        return $this->_runRequest(self::DO_CREATE_RECURRING_PAYMENTS_PROFILE);
    }

    public function getRecurringPaymentsProfileDetails($profileId)
    {
        $this->_setRequestData(
            array(
                 'PROFILEID' => $profileId
            )
        );
        return $this->_runRequest(self::DO_GET_RECURRING_PAYMENTS_PROFILE_DETAILS);
    }

    public function manageRecurringPaymentsProfileStatus($profileId, $action, $note)
    {
        $this->_setRequestData(
            array(
                 'PROFILEID' => $profileId,
                 'ACTION'    => $action,
                 'NOTE'      => $note,
            )
        );
        return $this->_runRequest(self::DO_MANAGE_RECURRING_PAYMENTS_PROFILE_STATUS);
    }

    public function updateRecurringPaymentsProfile($profileId, $data, $note)
    {
        $this->_setRequestData(
            array_merge(
                $data,
                array(
                     'PROFILEID' => $profileId,
                     'NOTE'      => $note,
                )
            )
        );
        return $this->_runRequest(self::DO_UPDATE_RECURRING_PAYMENTS_PROFILE);
    }

    public function billOutstandingAmount($profileId, $amount, $note)
    {
        $this->_setRequestData(
            array(
                 'PROFILEID' => $profileId,
                 'AMT'       => $amount,
                 'NOTE'      => $note,
            )
        );
        return $this->_runRequest(self::DO_BILL_OUTSTANDING_AMOUNT);
    }

    /**
     * @param array $data
     */
    public function setConfigData($data)
    {
        $this->_config = $data;
    }

    protected function _setRequestData($data)
    {
        $this->_client->setData('request', $data);
    }

    protected function _getApiVersion()
    {
        return '72.0';
    }

    protected function _runRequest($command)
    {
        if (!isset($this->_config['sandbox_flag'])) {
            throw new Exception('Some config fields are not specified');
        }
        $apiAuthentication = false;
        if (array_key_exists('api_authentication', $this->_config)) {
            $apiAuthentication = $this->_config['api_authentication'];
        }
        $url = $apiAuthentication ? 'https://api%s.paypal.com/nvp' : 'https://api-3t%s.paypal.com/nvp';
        $url = sprintf($url, $this->_config['sandbox_flag'] ? '.sandbox' : '');
        curl_setopt($this->_client->getHandler(), CURLOPT_URL, $url);

        if (
            !isset($this->_config['username'])
            || !isset($this->_config['password'])
            || !isset($this->_config['signature'])
        ) {
            throw new Exception('Some authenticate config fields are not specified');
        }

        if (
            !isset($this->_serviceRequestRequiredFieldsMap[$command])
            || !isset($this->_serviceRequestOptionalFieldsMap[$command])
            || !isset($this->_serviceResponseFieldsMap[$command])
        ) {
            throw new Exception('Service map does not specified');
        }
        $requestData = array_merge(
            $this->_client->getData('request'),
            array(
                 'METHOD'  => $command,
                 'VERSION' => $this->_getApiVersion(),
                 'USER'    => $this->_config['username'],
                 'PWD'     => $this->_config['password'],
            )
        );
        if (!$this->_config['api_authentication']) {
            $requestData = array_merge(
                $requestData,
                array(
                     'SIGNATURE' => $this->_config['signature'],
                )
            );
        } else {
            $websiteId = Mage::app()->getWebsite()->getId();
            $certPath = Mage::getModel('paypal/cert')->loadByWebsite($websiteId, false)->getCertPath();
            curl_setopt($this->_client->getHandler(), CURLOPT_SSLCERT, $certPath);
        }
        $requiredFields = array_merge(
            array_intersect_key(array_flip($this->_serviceRequestRequiredFieldsMap[$command]), $requestData),
            array_intersect_key(array_flip($this->_serviceRequestEachCallRequiredFields), $requestData)
        );
        $notSpecifiedRequiredFields = array_merge(
            array_diff_key(array_flip($this->_serviceRequestRequiredFieldsMap[$command]), $requiredFields),
            array_diff_key(array_flip($this->_serviceRequestEachCallRequiredFields), $requiredFields)
        );
        if (count($notSpecifiedRequiredFields) > 0) {
            $keysAsString = implode(', ', array_keys($notSpecifiedRequiredFields));
            throw new Exception('Some required fields a not specified: ' . $keysAsString);
        }
        $notSpecifiedFields = array_diff_key(
            $requestData,
            array_flip($this->_serviceRequestEachCallRequiredFields),
            array_flip($this->_serviceRequestRequiredFieldsMap[$command]),
            array_flip($this->_serviceRequestOptionalFieldsMap[$command])
        );
        if (count($notSpecifiedFields) > 0) {
            $keysAsString = implode(', ', array_keys($notSpecifiedFields));
            throw new Exception('Some fields a not specified in service map: ' . $keysAsString);
        }
        curl_setopt($this->_client->getHandler(), CURLOPT_POSTFIELDS, http_build_query($requestData));
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

    private function _parseResponse($httpResponse)
    {
        $httpResponseAsArray = explode("&", $httpResponse);
        $response = array();
        foreach ($httpResponseAsArray as $item) {
            $keyValue = explode("=", $item);
            if (count($keyValue) > 1) {
                $response[$keyValue[0]] = urldecode($keyValue[1]);
            }
        }
        return $response;
    }

    private function _isCallSuccessful($response)
    {
        if (!isset($response['ACK'])) {
            return false;
        }

        $ack = strtoupper($response['ACK']);
        $this->_callWarnings = array();
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
            // collect warnings
            if ($ack == 'SUCCESSWITHWARNING') {
                for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
                    $this->_callWarnings[] = $response["L_ERRORCODE{$i}"];
                }
            }
            return true;
        }
        return false;
    }

    private function _handleCallErrors($response)
    {
        $errors = array();
        for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
            $longMessage = isset($response["L_LONGMESSAGE{$i}"])
                ? preg_replace('/\.$/', '', $response["L_LONGMESSAGE{$i}"]) : '';
            $shortMessage = preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"]);
            $errors[] = $longMessage
                ? sprintf('%s (#%s: %s).', $longMessage, $response["L_ERRORCODE{$i}"], $shortMessage)
                : sprintf('#%s: %s.', $response["L_ERRORCODE{$i}"], $shortMessage);
            $this->_callErrors[] = $response["L_ERRORCODE{$i}"];
        }
        if ($errors) {
            $errors = implode(' ', $errors);
            throw new AW_Sarp2_Model_Engine_Paypal_Service_Exception($errors);
        }
    }
}