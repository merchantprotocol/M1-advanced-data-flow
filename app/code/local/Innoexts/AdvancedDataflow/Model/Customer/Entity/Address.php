<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Customer address entity
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Customer_Entity_Address extends Innoexts_AdvancedDataflow_Model_Entity_Entity
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