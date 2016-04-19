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


class AW_Sarp2_Model_Subscription_Type extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_sarp2/subscription_type');
    }

    /**
     * @return AW_Sarp2_Model_Engine_EngineInterface|null
     */
    public function getEngineModel()
    {
        return Mage::helper('aw_sarp2/engine')->getEngineModelByCode($this->getData('engine_code'));
    }

    /**
     * @return mixed
     */
    public function getAssociatedProfileCollection()
    {
        $collection = Mage::getModel('aw_sarp2/profile')->getCollection();
        $collection->addSubscriptionTypeFilter($this);
        return $collection;
    }

    /**
     * @throws AW_Sarp2_Model_Subscription_TypeException
     */
    public function validate()
    {
        $engine = $this->getEngineModel();
        if (is_null($engine)) {
            throw new AW_Sarp2_Model_Subscription_TypeException('Engine is not specified');
        }

        $data = array(
            'interval'     => array(
                'unit'   => $this->getData('period_unit'),
                'length' => (int)$this->getData('period_length'),
            ),
            'total_cycles' => (int)$this->getData('period_number_of_occurrences'),
        );
        if ($this->getData('trial_is_enabled')) {
            $data['trial_cycles'] = (int)$this->getData('trial_number_of_occurrences');
        }
        try {
            $engine->getPaymentRestrictionsModel()->validateAll($data);
        } catch (Exception $e) {
            throw new AW_Sarp2_Model_Subscription_TypeException($e->getMessage());
        }
    }

    /**
     * Unserialize store_ids
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $storeIds = explode(',', $this->getData('store_ids'));
        $this->setData('store_ids', $storeIds);
        return parent::_afterLoad();
    }

    /**
     * Serialize store_ids
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if (is_array($this->getData('store_ids'))) {
            $storeIds = implode(',', $this->getData('store_ids'));
            $this->setData('store_ids', $storeIds);
        }
        return parent::_beforeSave();
    }
}