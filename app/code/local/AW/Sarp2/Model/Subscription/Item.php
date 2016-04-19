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


class AW_Sarp2_Model_Subscription_Item extends Mage_Core_Model_Abstract
{
    /**
     * @var AW_Sarp2_Model_Subscription_Type|null
     */
    protected $_typeModel = null;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_sarp2/subscription_item');
    }

    /**
     * @return AW_Sarp2_Model_Subscription_Type|null
     */
    public function getTypeModel()
    {
        if (!is_null($this->_typeModel)) {
            return $this->_typeModel;
        }
        if (is_null($this->getSubscriptionTypeId())) {
            return null;
        }
        $this->_typeModel = Mage::getModel('aw_sarp2/subscription_type')->load($this->getSubscriptionTypeId());
        if (is_null($this->_typeModel->getId())) {
            $this->_typeModel = null;
        }
        return $this->_typeModel;
    }

    /**
     * @param AW_Sarp2_Model_Subscription_Type $type
     *
     * @return AW_Sarp2_Model_Subscription_Item
     */
    public function setTypeModel(AW_Sarp2_Model_Subscription_Type $type)
    {
        $this->_typeModel = $type;
        return $this;
    }

    /**
     * @throws AW_Sarp2_Model_Subscription_TypeException
     */
    public function validate()
    {
        if (is_null($this->getTypeModel())) {
            throw new AW_Sarp2_Model_Subscription_TypeException("Subscription type is not defined");
        }
        try {
            $this->getTypeModel()->validate();
        } catch (Exception $e) {
            throw new AW_Sarp2_Model_Subscription_TypeException(
                Mage::helper('aw_sarp2')->__('Subscription type "%s" is invalid', $this->getTypeModel()->getTitle())
            );
        }

        $data = array(
            'amount' => $this->getData('regular_price'),
        );
        if ($this->getTypeModel()->getData('trial_is_enabled')) {
            $data = array(
                'trial_amount' => $this->getData('trial_price'),
            );
        }
        if ($this->getTypeModel()->getData('initial_fee_is_enabled')) {
            $data = array(
                'initial_amount' => $this->getData('initial_fee_price'),
            );
        }

        try {
            $this->getTypeModel()->getEngineModel()->getPaymentRestrictionsModel()->validateAll($data);
        } catch (Exception $e) {
            throw new AW_Sarp2_Model_Subscription_TypeException($e->getMessage());
        }
    }
}