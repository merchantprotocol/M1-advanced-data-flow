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

class AW_Sarp2_Authorizenet_SilentpostController extends Mage_Core_Controller_Front_Action
{
    const PAYMENT_METHOD_CODE = 'authorizenet';

    protected function _initProfile($requestParamName = 'x_subscription_id')
    {
        $profileId = (int)$this->getRequest()->getParam($requestParamName, 0);
        $profile = Mage::getModel('aw_sarp2/profile');
        if ($profileId === 0) {
            throw new Exception('Profile ID has not been specified');
        }
        $profile->loadByReferenceId($profileId);
        Mage::register('current_profile', $profile);
        return true;
    }

    public function indexAction()
    {
        $silentPostData = $this->getRequest()->getParams();
        try {
            $this->_initProfile();
        } catch (Exception $e) {
            exit;
        }

        $profile = Mage::registry('current_profile');
        if ($profile->getId()) {
            if ($this->_checkResponseResult() && $this->_checkMd5Hash()) {
                Mage::getModel('aw_sarp2/engine_authorizenet_payment_silentpost')->process($profile, $silentPostData);
            } else {
                $profile->synchronizeWithEngine();
            }
        }
    }

    protected function _checkResponseResult()
    {
        $xResponseCode = $this->getRequest()->getParam('x_response_code');
        $xResponseReasonCode = $this->getRequest()->getParam('x_response_reason_code');
        if (($xResponseCode == 1) && ($xResponseReasonCode == 1)) {
            return true;
        }
        return false;
    }

    protected function _checkMd5Hash()
    {
        $methodInstance = Mage::helper('payment')->getMethodInstance(self::PAYMENT_METHOD_CODE);
        $configMd5HashValue = $methodInstance->getConfigData('md5_hash') ?
            $methodInstance->getConfigData('md5_hash') : '';
        $xTransactionId = $this->getRequest()->getParam('x_trans_id');
        $xAmount = $this->getRequest()->getParam('x_amount');
        $xMd5HashValue = $this->getRequest()->getParam('x_MD5_Hash');
        if (strtoupper($xMd5HashValue) == strtoupper(md5($configMd5HashValue . $xTransactionId . $xAmount))) {
            return true;
        }
        return false;
    }
}