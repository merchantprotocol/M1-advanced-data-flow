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

/**
 * DATA structure in details/initial_details
 * array(
 *     method_code => string,
 *     description => string,
 *     order_info => array(),
 *     billing_address => array(),
 *     shipping_address => array(),
 *     order_item_info => array(),
 *     currency_code => string,
 *     store_id => int,
 *     billing_amount => float,
 *     shipping_amount => float,
 *     tax_amount => float,
 *     subscription => array('general' => array(), 'item' => array(), 'type' => array())
 * )
 */
class AW_Sarp2_Model_Profile extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'aw_sarp2_profile';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'aw_sarp2_profile';

    protected $_subscriptionItem = null;
    protected $_subscriptionType = null;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_sarp2/profile');
    }


    /**
     * @param $referenceId
     *
     * @return Mage_Core_Model_Abstract
     */
    public function loadByReferenceId($referenceId)
    {
        return $this->load($referenceId, 'reference_id');
    }

    /**
     * @return AW_Sarp2_Model_Subscription_Item|null
     */
    public function getSubscriptionItem()
    {
        if (is_null($this->_subscriptionItem)) {
            $data = $this->getData('details/subscription/item');
            $type = $this->getSubscriptionType();
            if (is_null($data) || is_null($type)) {
                return null;
            }
            $item = Mage::getModel('aw_sarp2/subscription_item');
            $item->setData($data);
            $item->setTypeModel($type);
            $this->_subscriptionItem = $item;
        }
        return $this->_subscriptionItem;
    }

    /**
     * @return AW_Sarp2_Model_Subscription_Type|null
     */
    public function getSubscriptionType()
    {
        if (is_null($this->_subscriptionType)) {
            $data = $this->getData('details/subscription/type');
            if (is_null($data)) {
                return null;
            }
            $type = Mage::getModel('aw_sarp2/subscription_type');
            $type->setData($data);
            $this->_subscriptionType = $type;
        }
        return $this->_subscriptionType;
    }

    /**
     * @return AW_Sarp2_Model_Engine_EngineInterface|null
     */
    public function getSubscriptionEngineModel()
    {
        $engineCode = $this->getSubscriptionEngineCode();
        return Mage::helper('aw_sarp2/engine')->getEngineModelByCode($engineCode);
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        $engineModel = $this->getSubscriptionEngineModel();
        if (!is_null($engineModel)) {
            return $engineModel->getStatusSource()->getStatusLabel($this->getStatus());
        }
        return null;
    }

    /**
     * @return string
     */
    public function getEngineLabel()
    {
        return Mage::helper('aw_sarp2/engine')->getEngineLabelByCode($this->getSubscriptionEngineCode());
    }


    /**
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function getLinkedOrderCollection()
    {
        $profileOrderCollection = Mage::getResourceModel('aw_sarp2/profile_order_collection')->addProfileFilter($this);
        return $profileOrderCollection->getLinkedOrderCollection();
    }

    /**
     * @throws AW_Sarp2_Model_ProfileException
     */
    public function synchronizeWithEngine()
    {
        try {
            $data = $this->getSubscriptionEngineModel()->getRecurringProfileDetails($this);
            Mage::helper('aw_sarp2/profile')->importDataToProfile($data, $this);
            $this->save();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new AW_Sarp2_Model_ProfileException("Unable synchronize profile with engine");
        }
    }

    /**
     * @param string $note
     *
     * @throws AW_Sarp2_Model_ProfileException
     */
    public function changeStatusToActive($note)
    {
        if (!$this->canActivate() || !$this->getSubscriptionEngineModel()) {
            throw new AW_Sarp2_Model_ProfileException("Unable activate subscription");
        }
        try {
            $this->getSubscriptionEngineModel()->updateStatusToActive($this, $note);
            $this->synchronizeWithEngine();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new AW_Sarp2_Model_ProfileException("Unable activate subscription");
        }
    }

    /**
     * @param string $note
     *
     * @throws AW_Sarp2_Model_ProfileException
     */
    public function changeStatusToSuspend($note)
    {
        if (!$this->canSuspend() || !$this->getSubscriptionEngineModel()) {
            throw new AW_Sarp2_Model_ProfileException("Unable suspend subscription");
        }
        try {
            $this->getSubscriptionEngineModel()->updateStatusToSuspended($this, $note);
            $this->synchronizeWithEngine();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new AW_Sarp2_Model_ProfileException("Unable suspend subscription");
        }
    }

    /**
     * @param string $note
     *
     * @throws AW_Sarp2_Model_ProfileException
     */
    public function changeStatusToCancel($note)
    {
        if (!$this->canCancel() || !$this->getSubscriptionEngineModel()) {
            throw new AW_Sarp2_Model_ProfileException("Unable cancel subscription");
        }
        try {
            $this->getSubscriptionEngineModel()->updateStatusToCanceled($this, $note);
            $this->synchronizeWithEngine();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new AW_Sarp2_Model_ProfileException("Unable cancel subscription");
        }
    }

    /**
     * @throws AW_Sarp2_Model_ProfileException
     */
    protected function _beforeSave()
    {
        $now = new Zend_Date();
        if (!$this->getId()) {
            $this->setData('initial_details', $this->getData('details'));
            $this->setCreatedAt($now->getIso());
        }
        $this->setUpdatedAt($now->getIso());
        return parent::_beforeSave();
    }

    /**
     * copy-past from Mage_Sales_Model_Recurring_Profile
     *
     * @throws AW_Sarp2_Model_ProfileException
     */
    public function createOrder()
    {
        $items = array();
        $itemInfoObjects = func_get_args();

        $billingAmount = 0;
        $shippingAmount = 0;
        $taxAmount = 0;
        $isVirtual = 1;
        $weight = 0;
        foreach ($itemInfoObjects as $itemInfo) {
            $item = $this->_getItem($itemInfo);
            $billingAmount += $item->getBasePrice();
            $shippingAmount += $item->getBaseShippingAmount();
            $taxAmount += $item->getBaseTaxAmount();
            $weight += $item->getWeight();
            if (!$item->getIsVirtual()) {
                $isVirtual = 0;
            }
            $items[] = $item;
        }
        $grandTotal = $billingAmount + $shippingAmount + $taxAmount;

        $order = Mage::getModel('sales/order');

        $billingAddress = Mage::getModel('sales/order_address')
            ->setData($this->getData('details/billing_address'))
            ->setId(null);

        $shippingInfo = $this->getData('details/shipping_address');
        $shippingAddress = Mage::getModel('sales/order_address')
            ->setData($shippingInfo)
            ->setId(null);

        $payment = Mage::getModel('sales/order_payment')
            ->setMethod($this->getData('details/method_code'));

        $transferDataKays = array(
            'store_id',             'store_name',           'customer_id',          'customer_email',
            'customer_firstname',   'customer_lastname',    'customer_middlename',  'customer_prefix',
            'customer_suffix',      'customer_taxvat',      'customer_gender',      'customer_is_guest',
            'customer_note_notify', 'customer_group_id',    'customer_note',        'shipping_method',
            'shipping_description', 'base_currency_code',   'global_currency_code', 'order_currency_code',
            'store_currency_code',  'base_to_global_rate',  'base_to_order_rate',   'store_to_base_rate',
            'store_to_order_rate'
        );

        $orderInfo = $this->getData('details/order_info');
        foreach ($transferDataKays as $key) {
            if (isset($orderInfo[$key])) {
                $order->setData($key, $orderInfo[$key]);
            } elseif (isset($shippingInfo[$key])) {
                $order->setData($key, $shippingInfo[$key]);
            }
        }

        $store = Mage::app()->getStore($this->getData('details/store_id'));
        $order->setStoreId($this->getData('details/store_id'))
            ->setState(Mage_Sales_Model_Order::STATE_NEW)
            ->setBaseToOrderRate($this->getData('details/order_info/base_to_quote_rate'))
            ->setStoreToOrderRate($this->getData('details/order_info/store_to_quote_rate'))
            ->setOrderCurrencyCode($this->getData('details/order_info/quote_currency_code'))
            ->setBaseSubtotal($billingAmount)
            ->setSubtotal($store->convertPrice($billingAmount))
            ->setBaseShippingAmount($shippingAmount)
            ->setShippingAmount($store->convertPrice($shippingAmount))
            ->setBaseTaxAmount($taxAmount)
            ->setTaxAmount($store->convertPrice($taxAmount))
            ->setBaseGrandTotal($grandTotal)
            ->setGrandTotal($store->convertPrice($grandTotal))
            ->setIsVirtual($isVirtual)
            ->setWeight($weight)
            ->setTotalQtyOrdered($this->getData('details/order_info/items_qty'))
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->setPayment($payment)
            ->setCustomerId($this->getData('customer_id'))
        ;

        foreach ($items as $item) {
            $order->addItem($item);
        }

        return $order;
    }

    /**
     * @param integer $orderId
     *
     * @return AW_Sarp2_Model_Profile
     */
    public function addOrderRelation($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        if (is_null($order->getId())) {
            return $this;
        }
        $profileOrder = Mage::getModel('aw_sarp2/profile_order');
        $profileOrder->setProfileId($this->getId());
        $profileOrder->setOrderId($order->getId());
        $profileOrder->save();
        //update field on profile
        $this->setLastOrderId($order->getId());
        $this->setLastOrderDate($order->getCreatedAt());
        $this->save();
        return $this;
    }

    /**
     * @return bool
     */
    public function canCancel()
    {
        if (is_null($this->getSubscriptionEngineModel())) {
            return false;
        }
        $status = $this->getStatus();
        $availableOperations = $this->getSubscriptionEngineModel()->getPaymentRestrictionsModel()
            ->getAvailableSubscriptionOperations($status);
        return in_array('cancel', $availableOperations);
    }

    /**
     * @return bool
     */
    public function canSuspend()
    {
        if (is_null($this->getSubscriptionEngineModel())) {
            return false;
        }
        $status = $this->getStatus();
        $availableOperations = $this->getSubscriptionEngineModel()->getPaymentRestrictionsModel()
            ->getAvailableSubscriptionOperations($status);
        return in_array('suspend', $availableOperations);
    }

    /**
     * @return bool
     */
    public function canActivate()
    {
        if (is_null($this->getSubscriptionEngineModel())) {
            return false;
        }
        $status = $this->getStatus();
        $availableOperations = $this->getSubscriptionEngineModel()->getPaymentRestrictionsModel()
            ->getAvailableSubscriptionOperations($status);
        return in_array('activate', $availableOperations);
    }

    /**
     * @return bool
     */
    public function canUpdate()
    {
        if (is_null($this->getSubscriptionEngineModel())) {
            return false;
        }
        $status = $this->getStatus();
        $availableOperations = $this->getSubscriptionEngineModel()->getPaymentRestrictionsModel()
            ->getAvailableSubscriptionOperations($status);
        return in_array('update', $availableOperations);
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     *
     * @param Varien_Object $itemInfo
     *
     * @throws AW_Sarp2_Model_ProfileException
     * @return Mage_Sales_Model_Order_Item|null
     */
    private function _getItem(Varien_Object $itemInfo)
    {
        $paymentType = $itemInfo->getPaymentType();
        if (!$paymentType) {
            throw new AW_Sarp2_Model_ProfileException("Recurring profile payment type is not specified.");
        }
        switch ($paymentType) {
            case Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_REGULAR:
                return $this->_getRegularItem($itemInfo);
            case Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_TRIAL:
                return $this->_getTrialItem($itemInfo);
            case Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_INITIAL:
                return $this->_getInitialItem($itemInfo);
            default:
                new AW_Sarp2_Model_ProfileException("Invalid recurring profile payment type '{$paymentType}'.");
        }
        return null;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for regular payment
     *
     * @param Varien_Object $itemInfo
     *
     * @return Mage_Sales_Model_Order_Item
     */
    private function _getRegularItem(Varien_Object $itemInfo)
    {
        $price = $itemInfo->getPrice() ? $itemInfo->getPrice() : $this->getData('amount');
        if ($itemInfo->getShippingAmount()) {
            $shippingAmount = $itemInfo->getShippingAmount();
        } else {
            $shippingAmount = $this->getData('details/shipping_amount');
        }
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : $this->getData('details/tax_amount');

        $store = Mage::app()->getStore($this->getData('details/store_id'));
        $item = Mage::getModel('sales/order_item')
            ->setData($this->getData('details/order_item_info'))
            ->setProductOptions($this->getData('details/order_item_info/product_options'))
            ->setQtyOrdered($this->getData('details/order_item_info/qty'))
            ->setBaseOriginalPrice($this->getData('amount'))
            ->setPrice($store->convertPrice($price))
            ->setBasePrice($price)
            ->setRowTotal($store->convertPrice($price))
            ->setBaseRowTotal($price)
            ->setTaxAmount($store->convertPrice($taxAmount))
            ->setBaseTaxAmount($taxAmount)
            ->setShippingAmount($store->convertPrice($shippingAmount))
            ->setBaseShippingAmount($shippingAmount)
            ->setId(null)
        ;
        return $item;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for trial payment
     *
     * @param Varien_Object $itemInfo
     *
     * @return Mage_Sales_Model_Order_Item
     */
    private function _getTrialItem(Varien_Object $itemInfo)
    {
        $item = $this->_getRegularItem($itemInfo);

        $item->setName(
            Mage::helper('aw_sarp2')->__('Trial %s', $item->getName())
        );

        $option = array(
            'label' => Mage::helper('aw_sarp2')->__('Payment type'),
            'value' => Mage::helper('aw_sarp2')->__('Trial period payment')
        );

        $this->_addAdditionalOptionToItem($item, $option);

        return $item;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for initial payment
     *
     * @param Varien_Object $itemInfo
     *
     * @return Mage_Sales_Model_Order_Item
     */
    private function _getInitialItem(Varien_Object $itemInfo)
    {
        if ($itemInfo->getPrice()) {
            $price = $itemInfo->getPrice();
        } else {
            $price = $this->getData('details/subscription/item/initial_fee_price');
        }
        $shippingAmount = $itemInfo->getShippingAmount() ? $itemInfo->getShippingAmount() : 0;
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : 0;
        $store = Mage::app()->getStore($this->getData('details/store_id'));
        $item = Mage::getModel('sales/order_item')
            ->setStoreId($this->getData('details/store_id'))
            ->setProductType(Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL)
            ->setIsVirtual(1)
            ->setSku('initial_fee')
            ->setName(Mage::helper('aw_sarp2')->__('Recurring Profile Initial Fee'))
            ->setDescription('')
            ->setWeight(0)
            ->setQtyOrdered(1)
            ->setPrice($store->convertPrice($price))
            ->setOriginalPrice($store->convertPrice($price))
            ->setBasePrice($price)
            ->setBaseOriginalPrice($price)
            ->setRowTotal($store->convertPrice($price))
            ->setBaseRowTotal($price)
            ->setTaxAmount($store->convertPrice($taxAmount))
            ->setBaseTaxAmount($taxAmount)
            ->setShippingAmount($store->convertPrice($shippingAmount))
            ->setBaseShippingAmount($shippingAmount)
        ;

        $option = array(
            'label' => Mage::helper('sales')->__('Payment type'),
            'value' => Mage::helper('sales')->__('Initial period payment')
        );

        $this->_addAdditionalOptionToItem($item, $option);
        return $item;
    }

    /**
     * Add additional options suboption into itev
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array                       $option
     */
    private function _addAdditionalOptionToItem($item, $option)
    {
        $options = $item->getProductOptions();
        $additionalOptions = $item->getProductOptionByCode('additional_options');
        if (is_array($additionalOptions)) {
            $additionalOptions[] = $option;
        } else {
            $additionalOptions = array($option);
        }
        $options['additional_options'] = $additionalOptions;
        $item->setProductOptions($options);
    }
}