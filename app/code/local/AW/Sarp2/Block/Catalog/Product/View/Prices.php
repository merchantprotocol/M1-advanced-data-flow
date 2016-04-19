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


class AW_Sarp2_Block_Catalog_Product_View_Prices extends Mage_Core_Block_Template
{
    public function getProduct()
    {
        $product = Mage::registry('current_product');
        if (is_null($product) || is_null($product->getId())) {
            return null;
        }
        return $product;
    }

    public function getSubscription()
    {
        $product = $this->getProduct();
        $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($product->getId());
        if (is_null($subscription->getId())) {
            return null;
        }
        return $subscription;
    }

    public function getSubscriptionTypeOptionId()
    {
        return AW_Sarp2_Helper_Subscription::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID;
    }

    public function getNoSubscriptionValue()
    {
        return AW_Sarp2_Helper_Subscription::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE;
    }

    public function getSubscriptionTypePrices()
    {
        $prices = array();
        $subscriptionItemCollection = Mage::helper('aw_sarp2/subscription')
            ->getSubscriptionItemCollectionForDisplayingOnStore($this->getSubscription())
        ;
        foreach ($subscriptionItemCollection as $item) {
            $prices[$item->getId()] = array(
                'id'    => $item->getId(),
                'price' => Mage::app()->getStore()->convertPrice($item->getRegularPrice()),
            );
        }
        return Zend_Json::encode($prices);
    }

    protected function _canShow()
    {
        if (!Mage::helper('aw_sarp2')->isEnabled()) {
            return false;
        }
        if (is_null($this->getProduct()) || is_null($this->getSubscription())) {
            return false;
        }
        return true;
    }

    protected function _toHtml()
    {
        if (!$this->_canShow()) {
            return '';
        }
        return parent::_toHtml();
    }
}