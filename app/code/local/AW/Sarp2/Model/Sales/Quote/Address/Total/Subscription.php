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


class AW_Sarp2_Model_Sales_Quote_Address_Total_Subscription extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        //++HACK for remove subscription items from nonNominal items
        $newNonNominalItems = array();
        $nonNominalItems = $address->getAllNonNominalItems();
        foreach ($nonNominalItems as $item) {
            if (!Mage::helper('aw_sarp2/quote')->isQuoteItemIsSubscriptionProduct($item)) {
                $newNonNominalItems[] = $item;
            }
        }
        $address->setData('cached_items_nonnominal', $newNonNominalItems);
        //--HACK for remove subscription items from nonominal items

        $collector = Mage::getSingleton(
            'aw_sarp2/sales_quote_address_total_subscription_collector',
            array('store' => $address->getQuote()->getStore())
        );

        foreach ($collector->getCollectors() as $model) {
            $model->collect($address);
        }

        $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
        foreach ($items as $item) {
            $rowTotal = 0;
            $baseRowTotal = 0;
            $totalDetails = array();
            foreach ($collector->getCollectors() as $model) {
                $itemRowTotal = $model->getItemRowTotal($item);
                if ($model->getIsItemRowTotalCompoundable($item)) {
                    $rowTotal += $itemRowTotal;
                    $baseRowTotal += $model->getItemBaseRowTotal($item);
                    $isCompounded = true;
                } else {
                    $isCompounded = false;
                }
                if ((is_float($itemRowTotal) || $itemRowTotal > 0) && $label = $model->getLabel()) {
                    $totalDetails[] = new Varien_Object(
                        array(
                             'label'         => $label,
                             'amount'        => $itemRowTotal,
                             'is_compounded' => $isCompounded,
                        )
                    );
                }
            }
            $item->setSubscriptionRowTotal($rowTotal);
            $item->setBaseSubscriptionRowTotal($baseRowTotal);
            $item->setSubscriptionTotalDetails($totalDetails);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($address->getQuote());
        if ($items) {
            $address->addTotal(
                array(
                     'code'  => $this->getCode(),
                     'title' => Mage::helper('aw_sarp2')->__('Subscription Items'),
                     'items' => $items,
                     'area'  => 'footer',
                )
            );
        }
        return $this;
    }
}