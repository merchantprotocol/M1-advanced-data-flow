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


class AW_Sarp2_Model_Subscription extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_sarp2/subscription');
    }

    /**
     * @param integer
     *
     * @return AW_Sarp2_Model_Subscription
     */
    public function loadByProductId($productId)
    {
        return $this->load($productId, 'product_id');
    }

    /**
     * @return AW_Sarp2_Model_Resource_Subscription_Item_Collection
     */
    public function getItemCollection()
    {
        return Mage::getResourceModel('aw_sarp2/subscription_item_collection')
            ->addSubscriptionFilter($this)
            ->addSortOrder()
        ;
    }

    /**
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProductModel()
    {
        if (is_null($this->getProductId())) {
            return null;
        }
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        if (is_null($product->getId())) {
            return null;
        }
        return $product;
    }

    /**
     * @throws AW_Sarp2_Model_Subscription_TypeException
     */
    public function validate()
    {
        foreach ($this->getItemCollection() as $item) {
            $item->validate();
        }
    }
}