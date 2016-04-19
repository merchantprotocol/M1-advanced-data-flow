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


class AW_Sarp2_Model_Source_Profile_Status implements AW_Sarp2_Model_Source_SourceInterface
{
    const STATUS_DELIMETER = '-';

    const ACTIVE    = 'active';
    const SUSPENDED = 'suspended';
    const CANCELLED = 'cancelled';
    const ACTIVE_LABEL    = 'Active';
    const SUSPENDED_LABEL = 'Suspended';
    const CANCELLED_LABEL = 'Cancelled';

    protected static $_globalStatuses = array();
    protected static $_nonGlobalStatuses = array();

    /**
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('aw_sarp2');
        // process global statuses
        $statusArray = array(
            self::ACTIVE    => $helper->__(self::ACTIVE_LABEL),
            self::SUSPENDED => $helper->__(self::SUSPENDED_LABEL),
            self::CANCELLED => $helper->__(self::CANCELLED_LABEL),
        );

        // process non global statuses
        foreach ($this->getNonGlobalStatuses() as $engineCode => $nonGlobalStatuses) {
            foreach ($nonGlobalStatuses as $statusCode => $statusLabel) {
                $engineLabel = Mage::helper('aw_sarp2/engine')->getEngineLabelByCode($engineCode);
                $key = $engineCode . self::STATUS_DELIMETER . $statusCode;
                $statusArray[$key] = sprintf('[%s] %s', $engineLabel, $helper->__($statusLabel));
            }
        }
        return $statusArray;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('aw_sarp2');
        // process global statuses
        $optionArray = array(
            array(
                'value' => self::ACTIVE,
                'label' => $helper->__(self::ACTIVE_LABEL),
            ),
            array(
                'value' => self::SUSPENDED,
                'label' => $helper->__(self::SUSPENDED_LABEL),
            ),
            array(
                'value' => self::CANCELLED,
                'label' => $helper->__(self::CANCELLED_LABEL),
            ),
        );

        // process non global statuses
        foreach ($this->getNonGlobalStatuses() as $engineCode => $nonGlobalStatuses) {
            foreach ($nonGlobalStatuses as $statusCode => $statusLabel) {
                $engineLabel = Mage::helper('aw_sarp2/engine')->getEngineLabelByCode($engineCode);
                $key = $engineCode . self::STATUS_DELIMETER . $statusCode;
                $optionArray[] = array(
                    'value' => $key,
                    'label' => sprintf('[%s] %s', $engineLabel, $helper->__($statusLabel)),
                );
            }
        }
        return $optionArray;
    }

    /**
     * @param $statusCode
     *
     * @return null|string
     */
    public function getStatusLabel($statusCode)
    {
        $statusArray = $this->toArray();
        if (isset($statusArray[$statusCode])) {
            return $statusArray[$statusCode];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getGlobalStatuses()
    {
        if ((count(self::$_globalStatuses) == 0)) {
            $this->_collectStatuses();
        }
        return self::$_globalStatuses;
    }

    /**
     * @return array
     */
    public function getNonGlobalStatuses()
    {
        if ((count(self::$_nonGlobalStatuses) == 0)) {
            $this->_collectStatuses();
        }
        return self::$_nonGlobalStatuses;
    }

    protected function _collectStatuses()
    {
        foreach (Mage::helper('aw_sarp2/engine')->toArray() as $engineCode => $engineLabel) {
            $engineModel = Mage::helper('aw_sarp2/engine')->getEngineModelByCode($engineCode);
            if (!is_null($engineModel)) {
                foreach ($engineModel->getStatusSource()->getGlobalStatusMap() as $key => $value) {
                    self::$_globalStatuses[$key][$engineCode] = $value;
                }
                foreach ($engineModel->getStatusSource()->getNonGlobalStatusMap() as $key => $value) {
                    self::$_nonGlobalStatuses[$engineCode][$key] = $value;
                }
            }
        }
    }
}