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


class AW_Sarp2_Model_Engine_Paypal_Payment_Ipn extends Mage_Paypal_Model_Ipn
{

    /** overwrite */
    public function processIpnRequest(array $request, Zend_Http_Client_Adapter_Interface $httpAdapter = null)
    {
        /* fix for 1411*/
        if (array_key_exists("period_type", $request)) {
            $request["period_type"] = trim($request["period_type"]);
        }
        $this->_request = $request;
        $this->_debugData = array('ipn' => $request);
        ksort($this->_debugData['ipn']);

        if (
            isset($this->_request['txn_type'])
            && in_array(
                $this->_request['txn_type'],
                array(
                     'recurring_payment_profile_created',
                     'recurring_payment_expired',
                     'recurring_payment_skipped',
                     'recurring_payment_suspended',
                     'recurring_payment_profile_cancel',
                )
            )
        ) {
            //change profile status via IPN request
            if (isset($this->_request['profile_status'])) {
                $profile = $this->_getRecurringProfile();
                $profile->setStatus($this->_request['profile_status']);
                try {
                    $profile->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            if ($httpAdapter) {
                $this->_postBack($httpAdapter);
            }
            //do nothing
            $this->_debug();
        } else {
            return parent::processIpnRequest($request, $httpAdapter);
        }
        return $this;
    }

    /**
     * @return AW_Sarp2_Model_Profile|null
     * @throws Exception
     */
    protected function _getRecurringProfile()
    {
        if (empty($this->_recurringProfile)) {
            if (!array_key_exists('recurring_payment_id', $this->_request)) {
                return parent::_getRecurringProfile();
            }
            $internalReferenceId = $this->_request['recurring_payment_id'];
            $this->_recurringProfile = Mage::getModel('aw_sarp2/profile')
                ->loadByReferenceId($internalReferenceId);
            if (!$this->_recurringProfile->getId()) {
                return parent::_getRecurringProfile();
            }
            // re-initialize config with the method code and store id
            $methodCode = $this->_recurringProfile->getData('details/method_code');
            $storeId = $this->_recurringProfile->getData('details/store_id');
            $this->_config = Mage::getModel(
                'paypal/config', array($methodCode, $storeId)
            );
            if (!$this->_config->isMethodActive($methodCode) || !$this->_config->isMethodAvailable()) {
                throw new Exception(sprintf('Method "%s" is not available.', $methodCode));
            }
        }
        return $this->_recurringProfile;
    }
}