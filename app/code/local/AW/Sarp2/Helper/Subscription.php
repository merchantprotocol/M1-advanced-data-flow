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


class AW_Sarp2_Helper_Subscription
{
    /** just unique negative number */
    const SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID = -5071;
    const SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID = -3722;

    const SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE = 0;
    const SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_TITLE = 'No Subscription';

    /**
     * @param AW_Sarp2_Model_Subscription $subscription
     * @param Zend_Date|null              $date = null
     *
     * @return Zend_Date|null
     */
    public function calculateSubscriptionStartDateForSelectedDate(
        AW_Sarp2_Model_Subscription $subscription, $date = null
    )
    {
        switch ($subscription->getStartDateCode()) {
            case AW_Sarp2_Model_Source_Subscription_Startdate::MOMENT_OF_PURCHASE_CODE:
                $date = Mage::app()->getLocale()->storeDate(null, null, true);
                return $date;
                break;
            case AW_Sarp2_Model_Source_Subscription_Startdate::LAST_DAY_OF_CURRENT_MONTH_CODE:
                $date = Mage::app()->getLocale()->storeDate(null, null, true);
                $date->addMonth(1)->setDay(1)->subDay(1);
                return $date;
                break;
            case AW_Sarp2_Model_Source_Subscription_Startdate::EXACT_DAY_OF_MONTH_CODE:
                $date = Mage::app()->getLocale()->storeDate(null, null, true);
                if ($date->toString(Zend_Date::DAY_SHORT, 'iso') > $subscription->getDayOfMonth()) {
                    $date->addMonth(1);
                }
                $date->setDay($subscription->getDayOfMonth());
                return $date;
                break;
            case AW_Sarp2_Model_Source_Subscription_Startdate::DEFINED_BY_CUSTOMER_CODE:
                $zDate = Mage::app()->getLocale()->storeDate(null, null, true);
                $zDate->setDate($date);
                return $zDate;
            default:
                return null;
        }
    }

    /**
     * @param AW_Sarp2_Model_Subscription_Type $type
     * @param Mage_Core_Model_Store|null       $store = null
     *
     * @return boolean
     */
    public function isSubscriptionTypeVisibleOnStore(AW_Sarp2_Model_Subscription_Type $type, $store = null)
    {
        if (is_null($store)) {
            $store = Mage::app()->getStore();
        }
        if (!$type->getIsVisible()) {
            return false;
        }
        if (!in_array($store->getId(), $type->getWebsiteIds()) && !in_array(0, $type->getWebsiteIds())) {
            return false;
        }
        return true;
    }

    /**
     * @param AW_Sarp2_Model_Subscription $subscription
     * @param Mage_Catalog_Model_Product  $product
     * @param string                      $title
     * @param string                      $subscriptionTypeSelectorType
     *
     * @return AW_Sarp2_Helper_Subscription
     */
    public function addSubscriptionTypeSelectorToSubscription(
        AW_Sarp2_Model_Subscription $subscription, Mage_Catalog_Model_Product $product,
        $title, $subscriptionTypeSelectorType = Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
    )
    {
        $productHelper = Mage::helper('aw_sarp2/product');
        $subscriptionTypeSelector = $productHelper->createProductOption(
            $product, $subscriptionTypeSelectorType,
            self::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID, $title
        );

        $subscriptionItemCollection = $this->getSubscriptionItemCollectionForDisplayingOnStore($subscription);
        $defaultCheckedValue = $subscriptionItemCollection->getFirstItem()->getId();
        if (!$subscription->getIsSubscriptionOnly()) {
            $productHelper->addProductOptionValue(
                $subscriptionTypeSelector, self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE,
                Mage::helper('aw_sarp2')->__(self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_TITLE)
            );
            $defaultCheckedValue = self::SUBSCRIPTION_TYPE_SELECTOR_NO_SUBSCRIPTION_OPTION_VALUE;
        }
        /** ++ hack for default checked option in 1.7.x*/
        $preconfiguredValuesObject = $product->getPreconfiguredValues();
        if (!is_null($preconfiguredValuesObject)) {
            $currentOptionValue = $preconfiguredValuesObject
                ->getData('options/' . $subscriptionTypeSelector->getOptionId())
            ;
            if (is_null($currentOptionValue)) {
                $values = $product->getPreconfiguredValues()
                    ->setData('options', array($subscriptionTypeSelector->getOptionId() => $defaultCheckedValue))
                ;
                $product->setPreconfiguredValues($values);
            }
        }
        /** -- hack for default checked option in 1.7.x*/
        foreach ($subscriptionItemCollection as $item) {
            $productHelper->addProductOptionValue(
                $subscriptionTypeSelector,
                $item->getId(),
                $item->getTypeModel()->getTitle()
            );
        }
        $product->addOption($subscriptionTypeSelector);
        return $this;
    }

    /**
     * @param AW_Sarp2_Model_Subscription $subscription
     * @param Mage_Catalog_Model_Product  $product
     * @param string                      $title
     *
     * @return AW_Sarp2_Helper_Subscription
     */
    public function addSubscriptionStartDateOptionToSubscription(
        AW_Sarp2_Model_Subscription $subscription, Mage_Catalog_Model_Product $product, $title
    )
    {
        $productHelper = Mage::helper('aw_sarp2/product');
        if (
            $subscription->getStartDateCode() == AW_Sarp2_Model_Source_Subscription_Startdate::DEFINED_BY_CUSTOMER_CODE
        ) {
            $dateStart = $productHelper->createProductOption(
                $product, Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE,
                self::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID, $title
            );
            $product->addOption($dateStart);
        } else {
            $calculatedStartDate = Mage::helper('aw_sarp2/subscription')
                ->calculateSubscriptionStartDateForSelectedDate($subscription)
            ;
            if (is_null($calculatedStartDate)) {
                $startDateAsString = "start date has not been calculated";
            } else {
                $startDateAsString = Mage::helper('core')->formatDate(
                    $calculatedStartDate,
                    Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                    false
                );
            }
            $dateStart = $productHelper->createProductOption(
                $product, 'fake_type',
                self::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID,
                Mage::helper('aw_sarp2')->__("%s: %s", $title, $startDateAsString)
            );
            $product->addOption($dateStart);
        }
        return $this;
    }

    /**
     * @param AW_Sarp2_Model_Subscription $subscription
     * @param Mage_Catalog_Model_Product  $product
     * @param string                      $title
     *
     * @return AW_Sarp2_Helper_Subscription
     */
    public function addSubscriptionStartDateLabelOptionToSubscription(
        AW_Sarp2_Model_Subscription $subscription, Mage_Catalog_Model_Product $product, $title
    )
    {
        $productHelper = Mage::helper('aw_sarp2/product');
        $dateStart = $productHelper->createProductOption(
            $product, Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD,
            self::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID, $title
        );

        $product->addOption($dateStart);
        return $this;
    }

    public function getSubscriptionItemCollectionForDisplayingOnStore(
        AW_Sarp2_Model_Subscription $subscription, $store = null
    )
    {
        if (is_null($store)) {
            $store = Mage::app()->getStore();
        }
        $engineCode = Mage::helper('aw_sarp2/config')->getSubscriptionEngine($store);
        return $subscription->getItemCollection()
            ->addSubscriptionTypeVisibilityFilter()
            ->addSubscriptionTypeStoreIdFilter($store->getId())
            ->addEngineCodeFilter($engineCode)
            ->addSortOrder()
        ;
    }
}