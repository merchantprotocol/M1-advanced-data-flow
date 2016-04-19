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

class AW_Sarp2_Helper_Profile extends Mage_Core_Helper_Data
{
    /**
     *
     */
    const PROFILE_CONTROLLER_NAME = 'adminhtml_profile';

    /**
     * @return bool
     */
    public function isSuspendedProfileExist()
    {
        return Mage::getResourceModel('aw_sarp2/profile_collection')
            ->addStatusFilter(AW_Sarp2_Model_Source_Profile_Status::SUSPENDED)
            ->getSize() > 0;
    }

    /**
     * Check is current controller is profile
     *
     * @return bool
     */
    public function isProfileController()
    {
        if (
            (Mage::app()->getRequest()->getControllerModule() == $this->_getModuleName())
            && (Mage::app()->getRequest()->getControllerName() == self::PROFILE_CONTROLLER_NAME)
        ) {
            return true;
        }
        return false;
    }

    public function importQuoteToProfile(Mage_Sales_Model_Quote $quote, AW_Sarp2_Model_Profile $profile)
    {
        $details = new Varien_Object();

        if ($quote->getPayment() && $quote->getPayment()->getMethod()) {
            $details->setMethodCode($quote->getPayment()->getMethodInstance()->getCode());
        }

        $orderInfo = $quote->getData();
        $this->_cleanupArray($orderInfo);
        $details->setOrderInfo($orderInfo);

        $addressInfo = $quote->getBillingAddress()->getData();
        $this->_cleanupArray($addressInfo);
        $details->setBillingAddress($addressInfo);
        if (!$quote->isVirtual()) {
            $addressInfo = $quote->getShippingAddress()->getData();
            $this->_cleanupArray($addressInfo);
            $details->setShippingAddress($addressInfo);
        }

        $paymentInfo = $quote->getPayment()->getData();
        $this->_cleanupArray($paymentInfo);
        $paymentInfo['cc_number'] = $quote->getPayment()->getData('cc_number');
        $details->setPayment($paymentInfo);

        $profile->setCustomerId($quote->getCustomer()->getId());
        $customerInfo = $quote->getCustomer()->getData();
        $this->_cleanupArray($customerInfo);
        $details->setCustomer($customerInfo);
        $details->setCurrencyCode($quote->getBaseCurrencyCode());
        $details->setStoreId($quote->getStoreId());

        foreach (Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($quote) as $item) {
            $details->setBillingAmount((float)$item->getBaseRowTotal())
                ->setTaxAmount((float)$item->getBaseTaxAmount())
                ->setShippingAmount((float)$item->getBaseShippingAmount())
            ;
            $details->setDescription($this->generateDescription($item));

            $orderItemInfo = $item->getData();
            $this->_cleanupArray($orderItemInfo);

            $customOptions = $item->getOptionsByCode();
            if ($customOptions['info_buyRequest']) {
                $orderItemInfo['info_buyRequest'] = $customOptions['info_buyRequest']->getValue();
            }
            $itemProductOptions = $item->getProductOrderOptions();
            if (!$itemProductOptions) {
                $itemProductOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            }
            //remove sarp options
            if (array_key_exists('options', $itemProductOptions)) {
                $sarpOptions = array(
                    AW_Sarp2_Helper_Subscription::SUBSCRIPTION_START_DATE_PRODUCT_OPTION_ID,
                    AW_Sarp2_Helper_Subscription::SUBSCRIPTION_TYPE_SELECTOR_PRODUCT_OPTION_ID
                );
                foreach ($itemProductOptions['options'] as $optionKey => $option) {
                    if (in_array($option['option_id'], $sarpOptions)) {
                        unset($itemProductOptions['options'][$optionKey]);
                    }
                }
            }
            $orderItemInfo['product_options'] = $itemProductOptions;

            $details->setOrderItemInfo($orderItemInfo);
            $subscription = Mage::getModel('aw_sarp2/subscription')->loadByProductId($item->getProductId());
            $subscriptionTypeOption = Mage::helper('aw_sarp2/quote')->getSubscriptionTypeOptionFromQuoteItem($item);
            $subscriptionItem = Mage::getModel('aw_sarp2/subscription_item')->load($subscriptionTypeOption);
            $subscriptionData = array(
                'general' => $subscription->getData(),
                'item'    => $subscriptionItem->getData(),
                'type'    => $subscriptionItem->getTypeModel()->getData(),
            );
            $details->setSubscription($subscriptionData);

            $startDate = Mage::helper('aw_sarp2/quote')->getSubscriptionStartDateOptionFromQuoteItem($item);
            $profile->setData('start_date', $startDate);
            $profile->setData('amount', (float)$item->getBaseRowTotal());
            $profile->setData('subscription_type_id', $subscriptionItem->getTypeModel()->getId());
        }
        $profile->setData(
            'subscription_engine_code', $subscriptionItem->getTypeModel()->getEngineModel()->getEngineCode()
        );
        $profile->setDetails($details->toArray());
        return $this;
    }

    public function importDataToProfile($data, AW_Sarp2_Model_Profile $profile, $extraKey = "")
    {
        $profileData = $profile->getData();
        foreach ($data as $key => $value) {
            $extra = ($extraKey !== "") ? "{$extraKey}/{$key}" : $key;
            if (is_array($value)) {
                $this->importDataToProfile($value, $profile, $extra);
            } else {
                $levels = explode("/", $extra);
                $currentData = &$profileData;
                foreach ($levels as $pKey) {
                    if (!isset($currentData[$key])) {
                        $currentData[$pKey] = array();
                    }
                    $currentData = &$currentData[$pKey];
                }
                $currentData = $value;
            }
        }
        $profile->setData($profileData);
        return $this;
    }

    public function generateDescription(Mage_Sales_Model_Quote_Item $item)
    {
        $description = $this->__("Recurring profile for product: %s", $item->getName());
        return $description;
    }


    /**
     * Recursively cleanup array from objects
     *
     * @param array &$array
     */
    private function _cleanupArray(&$array)
    {
        if (!$array) {
            return;
        }
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                unset($array[$key]);
            } elseif (is_array($value)) {
                $this->_cleanupArray($array[$key]);
            }
        }
    }
}