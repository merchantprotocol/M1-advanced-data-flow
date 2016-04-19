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


class AW_Sarp2_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    //copypaste from Mage_Sales_Model_Quote_Address::getAllItems() in mage 1.7.0.2
    public function getAllItems()
    {
        // We calculate item list once and cache it in three arrays - all items, nominal, non-nominal
        $cachedItems = $this->_nominalOnly ? 'nominal' : ($this->_nominalOnly === false ? 'nonnominal' : 'all');
        $key = 'cached_items_' . $cachedItems;
        if (!$this->hasData($key)) {
            // For compatibility  we will use $this->_filterNominal to divide nominal items from non-nominal
            // (because it can be overloaded)
            // So keep current flag $this->_nominalOnly and restore it after cycle
            $wasNominal = $this->_nominalOnly;
            $this->_nominalOnly = true; // Now $this->_filterNominal() will return positive values for nominal items

            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $items = array();
            $nominalItems = array();
            $nonNominalItems = array();
            if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }
                    $items[] = $aItem;
                    if ($this->_filterNominal($aItem)) {
                        $nominalItems[] = $aItem;
                    } else {
                        $nonNominalItems[] = $aItem;
                    }
                }
            } else {
                /*
                * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? ($addressType == self::TYPE_BILLING)
                    : ($addressType == self::TYPE_SHIPPING);

                if ($canAddItems) {
                    foreach ($quoteItems as $qItem) {
                        if ($qItem->isDeleted()) {
                            continue;
                        }
                        $items[] = $qItem;
                        if ($this->_filterNominal($qItem)) {
                            $nominalItems[] = $qItem;
                        } else {
                            $nonNominalItems[] = $qItem;
                        }
                    }
                }
            }

            // Cache calculated lists
            $this->setData('cached_items_all', $items);
            $this->setData('cached_items_nominal', $nominalItems);
            $this->setData('cached_items_nonnominal', $nonNominalItems);

            $this->_nominalOnly = $wasNominal; // Restore original value before we changed it
        }

        $items = $this->getData($key);
        return $items;
    }

    public function validateMinimumAmount()
    {
        $storeId = $this->getQuote()->getStoreId();
        if (Mage::getStoreConfigFlag('sales/minimum_order/active', $storeId)
            && Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($this->getQuote())
        ) {
            return true;
        }
        return parent::validateMinimumAmount();
    }

    public function getFreeMethodWeight()
    {
        if ($this->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
            && Mage::helper('aw_sarp2/quote')->isQuoteHasSubscriptionProduct($this->getQuote())
        ) {
            if ($this->getSarpFreeMethodWeight()) {
                return $this->getSarpFreeMethodWeight();
            }
            $items = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($this->getQuote());
            $freeMethodWeight = 0;
            foreach ($items as $item) {
                if (!$item->getProduct()->isVirtual() && !$item->getFreeShipping()) {
                    $freeMethodWeight += $item->getRowWeight();
                }
            }
            return $freeMethodWeight;
        }
        return parent::getFreeMethodWeight();
    }
}