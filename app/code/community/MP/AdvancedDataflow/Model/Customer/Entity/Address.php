<?php
/**
 * Mage Plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mage Plugins Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://mageplugins.net/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mageplugins@gmail.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to https://www.mageplugins.net for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2018 Mage Plugins Inc. and affiliates (https://mageplugins.net/)
 * @license    https://mageplugins.net/commercial-license/  Mage Plugins Commercial License (MPCL 1.0)
 */

/**
 * Customer address entity
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Customer_Entity_Address extends MP_AdvancedDataflow_Model_Entity_Entity
{
    /**
     * Check if address is default shipping address
     * 
     * @return integer
     */
    protected function isShipping()
    {
        $model = $this->getModel();
        if ($model) {
            $customer = $model->getCustomer();
            if ($customer && $customer->getDefaultShipping() && (($customer->getDefaultShipping() == $model->getId()))) {
                return 1;
            } else return 0;
        } else return 0;
    }
    /**
     * Check if address is default billing address
     * 
     * @return integer
     */
    protected function isBilling()
    {
        $model = $this->getModel();
        if ($model) {
            $customer = $model->getCustomer();
            if ($customer && $customer->getDefaultBilling() && (($customer->getDefaultBilling() == $model->getId()))) {
                return 1;
            } else return 0;
        } else return 0;
    }
    /**
     * Get model data
     * 
     * @return array
     */
    protected function _getModelData($action = 'export')
    {
        $data = array();
        $model = $this->getModel();
        if ($model) {
            $prefix = $this->getPrefix(true);
            $data[$prefix.'is_shipping'] = $this->isShipping();
            $data[$prefix.'is_billing'] = $this->isBilling();
            $data = array_merge(parent::_getModelData(), $data);
        }
        return $data;
    }
}
