<?php
/**
 * Merchant Protocol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Merchant Protocol Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://merchantprotocol.com/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@merchantprotocol.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.merchantprotocol.com for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2016 Merchant Protocol LLC. and affiliates (https://merchantprotocol.com/)
 * @license    https://merchantprotocol.com/commercial-license/  Merchant Protocol Commercial License (MPCL 1.0)
 */

/**
 * Customer entity
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Customer_Entity_Customer extends Innoexts_AdvancedDataflow_Model_Entity_Entity
{
    /**
     * Newsletter model index
     * 
     * @var array
     */
    protected $_newsletterModelIndex;
    /**
     * Retrieve newsletter subscribers model
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    protected function getNewsletterModel()
    {
        if (is_null($this->_newsletterModelIndex)) {
            $object = Mage::getModel('newsletter/subscriber');
            $this->_newsletterModelIndex = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_newsletterModelIndex);
    }
    /**
     * Check if customer is subscribed
     * 
     * @return integer
     */
    protected function isSubscribed()
    {
        $model = $this->getModel();
        if ($model) {
            $newsletter = $this->getNewsletterModel()->setData(array())->loadByCustomer($model);
            return (
                $newsletter->getId() && ($newsletter->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
            ) ? 1 : 0;
        } else {
            return 0;
        }
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
            $data['is_subscribed'] = $this->isSubscribed();
            $data = array_merge(parent::_getModelData(), $data);
        }
        return $data;
    }
    /**
     * Load model by data
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function loadModelByData($data)
    {
        $model = $this->getModel();
        if ($model) {
            $helper = $this->getHelper();
            if (empty($data['website_id'])) {
                $message = $helper->__('Skipping import row, required field "%s" is not defined.', 'website');
                Mage::throwException($message);
            }
            $website = $this->getWebsite($data['website_id']);
            if ($website === false) {
                $message = $helper->__('Skipping import row, website "%s" field does not exist.', $data['website_id']);
                Mage::throwException($message);
            }
            if (empty($data['real_customer_id']) && empty($data['email'])) {
                $message = $helper->__('Skipping import row, customer identifier or email must be defined.');
                Mage::throwException($message);
            }
            if (!empty($data['real_customer_id'])) {
                $collection = $model->getResourceCollection()->addAttributeToSelect('*')
                    ->addAttributeToFilter('real_customer_id', $data['real_customer_id'])->setPage(1, 1);
                foreach ($collection as $object) { $model = $object; break; }
                $this->setModel($model);
            } else {
                $model->setWebsiteId($website->getId());
                $model->loadByEmail($data['email']);
            }
        }
        return $this;
    }
    /**
     * Save model
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function saveModelData($data)
    {
        $model = $this->getModel();
        if (empty($data['store_id'])) {
            $data['store_id'] = $this->getDefaultStore()->getId();
        }
        return parent::saveModelData($data);
    }
    /**
     * Save model
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function saveModel()
    {
        $model = $this->getModel();
        if ($model) {
            $model->setImportMode(true);
            
            #_deb($model->toArray());
            die();
            
            # $model->save();
            /*
            $customerChanged = false;
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                $customerAddress->setCustomerId($customer->getId());
                $customerAddress->save();
                if ($customerAddress->getDefaultShipping()) {
                    $customer->setDefaultShipping($customerAddress->getId());
                    $customerChanged = true;
                }
                if ($customerAddress->getDefaultBilling()) {
                    $customer->setDefaultBilling($customerAddress->getId());
                    $customerChanged = true;
                }
            }
            if ($customerChanged) $customer->save();
            */
        }
        return $this;
    }
}
