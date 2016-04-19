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
 * Order adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order extends Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
{
    /**
     * Statuses
     * 
     * @var array
     */
    protected $_statuses;
    /**
     * Shipping carriers
     * 
     * @var array
     */
    protected $_shippingCarriers;
    /**
     * Currencies
     * 
     * @var array
     */
    protected $_currencies;
    /**
     * Regions
     * 
     * @var array
     */
    protected $_regions;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setVar('entity_type', 'sales/order');
        if (!$this->hasOrder()) $this->setOrder(Mage::getModel('sales/order'));
    }
    /**
     * Check if order exists
     * 
     * @return boolean
     */
    public function hasOrder()
    {
        return (Mage::registry('Object_Cache_Order')) ? true : false;
    }
    /**
     * Set Order
     * 
     * @param Mage_Sales_Model_Order $order
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $id = Mage::objects()->save($order);
        Mage::register('Object_Cache_Order', $id);
        return $this;
    }
    /**
     * Get order
     * 
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_Order'));
    }
    /**
     * Initialize fields
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function initializeFields()
    {
        parent::initializeFields();
        $this->initializeEntityTypeFields('sales_order');
        $this->initializeEntityTypeFields('sales_order_address');
        $this->initializeEntityTypeFields('sales_order_item');
        $this->initializeEntityTypeFields('sales_order_payment');
        return $this;
    }
    /**
     * Get shipping address prefix
     * 
     * @param boolean $underscored
     * @return string
     */
    protected function getShippingAddressPrefix($underscored = false)
    {
        return 'shipping_address'.(($underscored) ? '_' : '');
    }
    /**
     * Get billing address prefix
     * 
     * @param boolean $underscored
     * @return string
     */
    protected function getBillingAddressPrefix($underscored = false)
    {
        return 'billing_address'.(($underscored) ? '_' : '');
    }
    /**
     * Get payment prefix
     * 
     * @param boolean $underscored
     * @return string
     */
    protected function getItemPrefix($underscored = false)
    {
        return 'item'.(($underscored) ? '_' : '');
    }
    /**
     * Get payment prefix
     * 
     * @param boolean $underscored
     * @return string
     */
    protected function getPaymentPrefix($underscored = false)
    {
        return 'payment'.(($underscored) ? '_' : '');
    }
    /**
     * Get joins
     * 
     * @return array
     */
    protected function getJoins()
    {
        $shippingAddressPrefix = $this->getShippingAddressPrefix();
        $billingAddressPrefix = $this->getBillingAddressPrefix();
        $itemPrefix = $this->getItemPrefix();
        $paymentPrefix = $this->getPaymentPrefix();
        return array(
            $shippingAddressPrefix => array(
                'table' => 'sales/order_address', 
            	'condition' => '((main_table.entity_id = '.$shippingAddressPrefix.'.parent_id) AND '.
            	    '('.$shippingAddressPrefix.'.address_type = \'shipping\'))', 
            ), 
            $billingAddressPrefix => array(
                'table' => 'sales/order_address', 
            	'condition' => '((main_table.entity_id = '.$billingAddressPrefix.'.parent_id) AND '.
            	    '('.$billingAddressPrefix.'.address_type = \'billing\'))', 
            ), 
            $itemPrefix => array(
                'table' => 'sales/order_item', 'condition' => '(main_table.entity_id = '.$itemPrefix.'.order_id)', 
            ), 
            $this->getPaymentPrefix() => array(
                'table' => 'sales/order_payment', 'condition' => '(main_table.entity_id = '.$paymentPrefix.'.parent_id)', 
            ), 
        );
    }
    /**
     * Get attributes filters
     * 
     * @return array
     */
    protected function getAttributesFilters()
    {
        $shippingAddressPrefix = $this->getShippingAddressPrefix(true);
        $billingAddressPrefix = $this->getBillingAddressPrefix(true);
        $itemPrefix = $this->getItemPrefix(true);
        $filters = array(
            'increment_id' => 'startsWith', 
            'real_order_id' => 'startsWith', 
            'coupon_code' => 'startsWith', 
        	'status' => 'eq', 
            'shipping_method' => 'eq', 
            'customer_group_id' => 'eq', 
            'customer_email' => 'like', 
            'customer_firstname' => 'startsWith', 
            'customer_lastname' => 'startsWith', 
            'created_at' => 'datetimeFromTo', 
            'updated_at' => 'datetimeFromTo', 
        	'weight' => 'fromTo', 
            'total_item_count' => 'fromTo', 
            'order_currency_code' => 'eq', 
            'shipping_amount' => 'fromTo', 
            'tax_amount' => 'fromTo', 
            'subtotal' => 'fromTo', 
            'grand_total' => 'fromTo', 
            'discount_amount' => 'fromTo', 
            'subtotal_incl_tax' => 'fromTo', 
            'total_due' => 'fromTo', 
            $shippingAddressPrefix.'email' => 'like', 
            $shippingAddressPrefix.'firstname' => 'startsWith', 
            $shippingAddressPrefix.'lastname' => 'startsWith', 
            $shippingAddressPrefix.'company' => 'like', 
            $shippingAddressPrefix.'city' => 'like', 
            $shippingAddressPrefix.'country_id' => 'eq', 
            $shippingAddressPrefix.'region' => 'like', 
            $shippingAddressPrefix.'postcode' => 'eq', 
            $shippingAddressPrefix.'telephone' => 'startsWith', 
            $billingAddressPrefix.'email' => 'like', 
            $billingAddressPrefix.'firstname' => 'startsWith', 
            $billingAddressPrefix.'lastname' => 'startsWith', 
            $billingAddressPrefix.'company' => 'like', 
            $billingAddressPrefix.'city' => 'like', 
            $billingAddressPrefix.'country_id' => 'eq', 
            $billingAddressPrefix.'region' => 'like', 
            $billingAddressPrefix.'postcode' => 'eq', 
            $billingAddressPrefix.'telephone' => 'startsWith', 
            $itemPrefix.'sku' => 'startsWith', 
            $itemPrefix.'name' => 'like', 
            $itemPrefix.'qty_ordered' => 'eq', 
            $itemPrefix.'weight' => 'fromTo', 
            $itemPrefix.'row_weight' => 'fromTo', 
            $itemPrefix.'price' => 'fromTo', 
            $itemPrefix.'tax_amount' => 'fromTo', 
            $itemPrefix.'discount_amount' => 'fromTo', 
            $itemPrefix.'row_total' => 'fromTo', 
            $itemPrefix.'price_incl_tax' => 'fromTo', 
            $itemPrefix.'row_total_incl_tax' => 'fromTo', 
        );
        return $filters;
    }
    /**
     * Get attributes filters keys by type
     * 
     * @param string $type
     * @return array
     */
    protected function getAttributesFiltersKeysByType($type)
    {
        $keys = array();
        foreach ($this->getAttributesFilters() as $key => $filterType) {
            if ($filterType == $type) array_push($keys, $key);
        }
        return $keys;
    }
    /**
     * Prepare filters variables
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function prepareFiltersVars()
    {
        $datetimeAttributes = array('created_at', 'updated_at');
        foreach ($datetimeAttributes as $attribute) {
            $fromKey = 'filter/'.$attribute.'/from';
            $toKey = 'filter/'.$attribute.'/to';
            if ($var = $this->getVar($fromKey)) $this->setVar($fromKey, $var.' 00:00:00');
            if ($var = $this->getVar($toKey)) $this->setVar($toKey, $var.' 23:59:59');
        }
        return $this;
    }
    /**
     * Load orders
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    public function load()
    {
        $attributesFilters = $this->getAttributesFilters();
        $this->prepareFiltersVars();
        $attributesToDatabase = array();
        if ($this->getStoreId()) {
            $this->_filter[] = array('attribute' => 'store_id', 'eq' => $this->getStoreId());
        }
        $filters = $this->_parseVars();
        parent::setFilter($attributesFilters, $attributesToDatabase);
        foreach ($this->getAttributesFiltersKeysByType('fromTo') as $key) {
            $value = $this->getFieldValue($filters, $key);
            if ($value) {
                $this->_filter[] = array('attribute' => $key, 'from' => $value['from'], 'to' => $value['to']);
            }
        }
        return $this->_load();
    }
    /**
     * Retrieve collection for load
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _getCollectionForLoad($entityType)
    {
        return Mage::getResourceModel($entityType.'_collection');
    }
    /**
     * Add collection filters to map
     * 
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return Innoexts_DataflowOrder_Model_Convert_Adapter_Entity
     */
    protected function addCollectionFiltersToMap($collection)
    {
        $joins = $this->getJoins();
        $joinsKeys = array_keys($joins);
        $filters = $this->getFilter();
        foreach ($filters as $filter) {
            $field = $filter['attribute'];
            $tableAlias = 'main_table';
            $fieldDbName = $field;
            foreach ($joinsKeys as $joinKey) {
                $prefix = $joinKey.'_';
                if (substr($field, 0, strlen($prefix)) == $prefix) {
                    $tableAlias = $joinKey;
                    $fieldDbName = substr($field, strlen($prefix));
                    break;
                }
            }
            $collection->addFilterToMap($field, $tableAlias.'.'.$fieldDbName);
        }
        return $this;
    }
    /**
     * Add collection joins
     * 
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function addCollectionJoins($collection)
    {
        $joins = $this->getJoins();
        $joinsKeys = array_keys($joins);
        $activeJoinsKeys = array();
        $filters = $this->getFilter();
        foreach ($filters as $filter) {
            $field = $filter['attribute'];
            foreach ($joinsKeys as $joinKey) {
                $prefix = $joinKey.'_';
                if (substr($field, 0, strlen($prefix)) == $prefix) {
                    if (!in_array($joinKey, $activeJoinsKeys)) array_push($activeJoinsKeys, $joinKey);
                    break;
                }
            }
        }
        foreach ($activeJoinsKeys as $joinKey) {
            $join = $joins[$joinKey];
            $collection->getSelect()->joinLeft(array($joinKey => $collection->getTable($join['table'])), $join['condition']);
        }
        return $this;
    }
    /**
     * Add collection filters
     * 
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return Innoexts_DataflowOrder_Model_Convert_Adapter_Entity
     */
    protected function addCollectionFilters($collection)
    {
        $filters = $this->getFilter();
        foreach ($filters as $filter) {
            $field = $filter['attribute'];
            $condition = $filter;
            unset($condition['attribute']);
            $collection->addFieldToFilter($field, $condition);
        }
        return $this;
    }
    /**
     * Load entities identifiers
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function _load()
    {
        $helper = $this->getHelper();
        if (!($entityType = $this->getVar('entity_type'))
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Core_Model_Mysql4_Abstract)) {
            $this->addException($helper->__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
        }
        try {
            $collection = $this->_getCollectionForLoad($entityType);
            $this->addCollectionFiltersToMap($collection)
                ->addCollectionJoins($collection)
                ->addCollectionFilters($collection);
            $collection->distinct(true);
            $entityIds = $collection->getAllIds();
            $message = $helper->__('Loaded %d records', count($entityIds));
            $this->addException($message);
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = $helper->__('Problem loading the collection, aborting. Error: %s', $e->getMessage());
            $this->addException($message, Varien_Convert_Exception::FATAL);
        }
        $this->setData($entityIds);
        return $this;
    }
    /**
     * Retrieve attribute collection
     * 
     * @param string $entityType
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function getAttributeCollection($entityType)
    {
        $model = 'advanceddataflow/'.$entityType.'_attribute_collection';
        return Mage::getResourceModel($model);
    }
    /**
     * Retrieve shipping carriers
     * 
     * @return array
     */
    protected function getShippingCarriers()
    {
        if (is_null($this->_shippingCarriers)) { $this->_shippingCarriers = Mage::getSingleton('shipping/config')->getAllCarriers(); }
        return $this->_shippingCarriers;
    }
    /**
     * Retrieve shipping method description by code
     * 
     * @return array
     */
    protected function getShippingMethodDescriptionByCode($code)
    {
        $description = null;
        $carriers = $this->getShippingCarriers();
        foreach ($carriers as $carrierCode => $carrier) {
            $carrierMethods = $carrier->getAllowedMethods();
            if (!$carrierMethods) continue;
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $_code = $carrierCode.'_'.$methodCode;
                if (strtolower($code) == strtolower($_code)) {
                    $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
                    $description = $carrierTitle.' - '.$methodTitle;
                    break;
                }
            }
        }
        return $description;
    }
    /**
     * Retrieve statuses
     *
     * @return array
     */
    protected function getStatuses()
    {
        if (is_null($this->_statuses)) {
            $this->_statuses = array();
            $collection = Mage::getResourceModel('sales/order_status_collection')->joinStates()->orderByLabel();
            foreach ($collection as $status) { $this->_statuses[$status->getStatus()] = $status; }
        }
        return $this->_statuses;
    }
    /**
     * Get state by status
     * 
     * @param string $status
     * @return string
     */
    protected function getStateByStatus($status)
    {
        $state = null;
        $statuses = $this->getStatuses();
        if (isset($statuses[$status])) { $state = $statuses[$status]->getState(); }
        return $state;
    }
    /**
     * Get default status by state
     * 
     * @param string $state
     * @return string
     */
    protected function getDefaultStatusByState($state)
    {
        $status = null;
        $statuses = $this->getStatuses();
        foreach ($statuses as $status) {
            if (($status->getState() == $state) && ($status->getIsDefault())) {
                $status = $status->getStatus(); break;
            }
        }
        return $status;
    }
    /**
     * Get default status
     * 
     * @return string
     */
    protected function getDefaultStatus()
    {
        $status = $this->getVar('default_status', 'pending');
        $statuses = array_keys($this->getStatuses());
        if (in_array($status, $statuses)) return $status;
        else return 'pending';
    }
    /**
     * Get default state
     * 
     * @return string
     */
    protected function getDefaultState()
    {
        return $this->getStateByStatus($this->getDefaultStatus());
    }
    /**
     * Get currency
     * 
     * @param string $code
     * @return Mage_Directory_Model_Currency
     */
    protected function getCurrency($code)
    {
        if (is_null($this->_currencies)) { $this->_currencies = array(); }
        if (!isset($this->_currencies[$code])) {
            $this->_currencies[$code] = Mage::getModel('directory/currency')->load($code);
        }
        return $this->_currencies[$code];
    }
    /**
     * Get regions
     * 
     * @param mixed $countryId
     * @return array
     */
    protected function getRegions($countryId = null)
    {
        if (is_null($this->_regions)) {
            $this->_regions = array();
            $collection = Mage::getResourceModel('directory/region_collection');
            foreach ($collection as $region) {
                $this->_regions[$region->getCountryId()][$region->getId()] = $region;
            }
        }
        if (!empty($countryId)) return (isset($this->_regions[$countryId]) ? $this->_regions[$countryId] : array());
        else return $this->_regions;
    }
    /**
     * Get region
     * 
     * @param string $countryId
     * @param string $regionId
     * @return Mage_Directory_Model_Region
     */
    protected function getRegion($countryId, $regionId)
    {
        $regions = $this->getRegions($countryId);
        if (isset($regions[$regionId])) return $regions[$regionId];
        else return null;
    }
    /**
     * Get and load order by row
     * 
     * @param array $row
     * @return Mage_Sales_Model_Order
     */
    protected function getOrderByRow($row)
    {
        $order = $this->getOrder();
        $order->reset();
        if (!empty($row['increment_id'])) $order->load($row['increment_id'], 'increment_id');
        if (!$order->getId() && !empty($row['real_order_id'])) $order->load($row['real_order_id'], 'real_order_id');
        return $order;
    }
    /**
     * Prepare store
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Core_Model_Store
     */
    protected function prepareStore($order, $row)
    {
        $store = null;
        if (!empty($row['store'])) $store = $this->getStoreByCode($row['store']);
        if (!$store) {
            if ($order->getId()) $store = $this->getStoreById($order->getStoreId());
            else $store = $this->getDefaultStore();
        }
        if ($store) {
            if ($order->getStoreId() != $store->getId()) {
                $namePieces = array($store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName());
                $order->setStoreName(implode("\n", $namePieces));
            }
            $order->setStoreId($store->getId());
            $order->setStore($store);
        } else {
            $message = $helper->__('Skipping import row, store not found.');
            Mage::throwException($message);
        }
        return $store;
    }
    /**
     * Prepare status
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Sales_Model_Order
     */
    protected function prepareStatus($order, $row)
    {
        if (empty($row['state']) && !empty($row['status'])) 
            $order->setState($this->getStateByStatus($row['status']), $row['status']);
        else if (!empty($row['state']) && empty($row['status'])) 
            $order->setState($row['state'], $this->getDefaultStatusByState($row['state']));
        else if (empty($row['state']) && empty($row['status']) && !$order->getId()) 
            $order->setState($this->getDefaultState(), $this->getDefaultStatus());
        else 
            $order->setState($row['state'], $row['status']);
        if (empty($row['hold_before_state']) && !empty($row['hold_before_status'])) {
            $order->setHoldBeforeState($this->getStateByStatus($row['hold_before_status']));
            $order->setHoldBeforeStatus($row['hold_before_status']);
        } else if (!empty($row['hold_before_state']) && empty($row['hold_before_status'])) {
            $order->setHoldBeforeState($row['hold_before_state']);
            $order->setHoldBeforeStatus($this->getDefaultStatusByState($row['hold_before_state']));
        } else if (!empty($row['hold_before_state']) && !empty($row['hold_before_status'])) {
            $order->setHoldBeforeState($row['hold_before_state']);
            $order->setHoldBeforeStatus($row['hold_before_status']);
        }
        return $order;
    }
    /**
     * Prepare shipping
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Sales_Model_Order
     */
    protected function prepareShipping($order, $row)
    {
        if (!empty($row['shipping_method'])) {
            $order->setShippingMethod($row['shipping_method']);
            $order->setShippingDescription($this->getShippingMethodDescriptionByCode($row['shipping_method']));
        } elseif (isset($row['shipping_method'])) {
            $order->unsShippingMethod();
            $order->unsShippingDescription();
        }
        return $order;
    }
    /**
     * Prepare customer
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Sales_Model_Order
     */
    protected function prepareCustomer($order, $row)
    {
        $helper = $this->getHelper();
        $store = $order->getStore();
        $website = $store->getWebsite();
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($website->getId());
        if (!empty($row['customer_email'])) $customer->loadByEmail($row['customer_email']);
        if (
            !$customer->getId() && 
            ($order->getId() && $order->getCustomerId() && ($order->getStoreId() == $store->getId()))
        ) $customer->load($order->getCustomerId());
        if ($customer->getId()) {
            $order->setCustomer($customer);
            $order->setCustomerId($customer->getId());
            $order->setCustomerGroupId($customer->getGroupId());
            if (empty($row['customer_prefix'])) $row['customer_prefix'] = $customer->getPrefix();
            if (empty($row['customer_firstname'])) $row['customer_firstname'] = $customer->getFirstname();
            if (empty($row['customer_middlename'])) $row['customer_middlename'] = $customer->getMiddlename();
            if (empty($row['customer_lastname'])) $row['customer_lastname'] = $customer->getLastname();
            if (empty($row['customer_suffix'])) $row['customer_suffix'] = $customer->getSuffix();
        }
        if (!empty($row['customer_name']) && (empty($row['customer_firstname']) && empty($row['customer_lastname']))) {
            $name = $this->parseAddressName($row['customer_name']);
            if (count($name)) {
                if (!empty($name['firstname'])) $row['customer_firstname'] = $name['firstname'];
                if (!empty($name['middlename'])) $row['customer_middlename'] = $name['middlename'];
                if (!empty($name['lastname'])) $row['customer_lastname'] = $name['lastname'];
            }
        }
        $order->setCustomerIsGuest((($customer->getId()) ? 0 : 1));
        $this->copyData($order, $row, array(
            'customer_note_notify', 'customer_email', 'customer_firstname', 'customer_lastname', 'customer_middlename', 
            'customer_prefix', 'customer_suffix', 'customer_taxvat', 'customer_dob', 'customer_gender', 'customer_note', 
            'ext_customer_id', 
        ));
        return $order;
    }
    /**
     * Convert amount if currency changed
     * 
     * @param Varien_Object $object
     * @param array $row
     * @param string $key
     * @param Mage_Directory_Model_Currency $currency
     * @param string $currencyKey
     * @return mixed
     */
    protected function convertAmount($object, $row, $key, $currency, $currencyKey)
    {
        $amount = null;
        if (
            (!isset($row[$key]) || (is_null($row[$key]))) && 
            $object->getId() && $object->getData($key) && !empty($row[$currencyKey]) && 
            ($object->getData($currencyKey) != $row[$currencyKey])
        ) {
            $rate = $currency->getAnyRate($object->getData($currencyKey));
            if ($rate) $amount = round($object->getData($key) / $rate, 2);
        }
        return $amount;
    }
    /**
     * Prepare row amount
     * 
     * @param Varien_Object $object
     * @param array $row
     * @param string $key
     * @param Mage_Directory_Model_Currency $baseCurrency
     * @param Mage_Directory_Model_Currency $orderCurrency
     * @return array
     */
    protected function prepareRowAmount($object, $row, $key, $baseCurrency, $orderCurrency)
    {
        $baseKey = 'base_'.$key;
        if (!empty($row[$baseKey]) && empty($row[$key])) {
            $rate = $baseCurrency->getAnyRate($orderCurrency->getCode());
            if ($rate) $row[$key] = round($row[$baseKey] * $rate, 2);
        } else if (empty($row[$baseKey]) && !empty($row[$key])) {
            $rate = $orderCurrency->getAnyRate($baseCurrency->getCode());
            if ($rate) $row[$baseKey] = round($row[$key] * $rate, 2);
        }
        $baseAmount = $this->convertAmount($object, $row, $baseKey, $baseCurrency, 'base_currency_code');
        if ($baseAmount) $row[$baseKey] = $baseAmount;
        $amount = $this->convertAmount($object, $row, $key, $orderCurrency, 'order_currency_code');
        if ($amount) $row[$key] = $amount;
        return $row;
    }
    /**
     * Prepare amounts
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return array
     */
    protected function prepareAmounts($order, $row)
    {
        $helper = $this->getHelper();
        $store = $order->getStore();
        $globalCurrencyCode = (!empty($row['global_currency_code'])) ? $row['global_currency_code'] : null;
        if (!$globalCurrencyCode) $globalCurrencyCode = ($order->getId()) ? 
            $order->getGlobalCurrencyCode() : Mage::app()->getBaseCurrencyCode();
        $baseCurrencyCode = (!empty($row['base_currency_code'])) ? $row['base_currency_code'] : null;
        if (!$baseCurrencyCode) $baseCurrencyCode = ($order->getId()) ? 
            $order->getBaseCurrencyCode() : $store->getBaseCurrencyCode();
        $orderCurrencyCode = (!empty($row['order_currency_code'])) ? $row['order_currency_code'] : null;
        if (!$orderCurrencyCode) $orderCurrencyCode = ($order->getId()) ? 
            $order->getOrderCurrencyCode() : $store->getBaseCurrencyCode();
        $baseCurrency = $this->getCurrency($baseCurrencyCode);
        $orderCurrency = $this->getCurrency($orderCurrencyCode);
        $baseToGlobalRate = (!empty($row['base_to_global_rate'])) ? $row['base_to_global_rate'] : null;
        if (!$baseToGlobalRate) $baseToGlobalRate = ($order->getId() && ($globalCurrencyCode == $order->getGlobalCurrencyCode())) ? 
            $order->getBaseToGlobalRate() : $baseCurrency->getAnyRate($globalCurrencyCode);
        $baseToOrderRate = (!empty($row['base_to_order_rate'])) ? $row['base_to_order_rate'] : null;
        if (!$baseToOrderRate) $baseToOrderRate = ($order->getId() && ($orderCurrencyCode == $order->getOrderCurrencyCode())) ? 
            $order->getBaseToOrderRate() : $baseCurrency->getAnyRate($orderCurrencyCode);
        if (!$baseToGlobalRate) $baseToGlobalRate = 1;
        if (!$baseToOrderRate) $baseToOrderRate = 1;
        $row = array_merge($row, array(
            'global_currency_code' => $globalCurrencyCode, 'base_currency_code' => $baseCurrencyCode, 
            'store_currency_code' => $baseCurrencyCode, 'order_currency_code' => $orderCurrencyCode, 
            'base_to_global_rate' => $baseToGlobalRate, 'base_to_order_rate' => $baseToOrderRate, 
            'store_to_base_rate' => $baseToGlobalRate, 'store_to_order_rate' => $baseToOrderRate, 
        ));
        $amountsKeys = array(
            'shipping_amount', 'tax_amount', 'subtotal', 'grand_total', 'discount_amount', 'discount_canceled', 'discount_invoiced', 
            'discount_refunded', 'shipping_canceled', 'shipping_invoiced', 'shipping_refunded', 'shipping_tax_amount', 'shipping_tax_refunded', 
            'shipping_discount_amount', 'shipping_incl_tax', 'subtotal_canceled', 'subtotal_invoiced', 'subtotal_refunded', 'tax_canceled', 
            'tax_invoiced', 'tax_refunded', 'hidden_tax_amount', 'shipping_hidden_tax_amount', 'hidden_tax_invoiced', 'hidden_tax_refunded', 
            'total_canceled', 'total_invoiced', 'total_offline_refunded', 'total_online_refunded', 'total_paid', 'total_refunded', 
            'subtotal_incl_tax', 'adjustment_negative', 'adjustment_positive', 'total_due', 'custbalance_amount', 'total_invoiced_cost'
        );
        foreach ($amountsKeys as $amountKey) {
            $row = $this->prepareRowAmount($order, $row, $amountKey, $baseCurrency, $orderCurrency);
        }
        $order->setBaseCurrency($baseCurrency);
        $order->setOrderCurrency($orderCurrency);
        $copyKeys = array(
            'global_currency_code', 'base_currency_code', 'store_currency_code', 'order_currency_code', 
            'base_to_global_rate', 'base_to_order_rate', 'store_to_base_rate', 'store_to_order_rate', 
        );
        foreach ($amountsKeys as $amountKey) { array_push($copyKeys, $amountKey); array_push($copyKeys, 'base_'.$amountKey); }
        $this->copyData($order, $row, $copyKeys);
        return $order;
    }
    /**
     * Prepare address row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @param string $addressType
     * @param array $exclude
     * @return Mage_Sales_Model_Order_Address
     */
    protected function prepareAddress($order, $row, $addressType, $exclude = array())
    {
        $entityType = 'sales_order_address';
        $row = $this->extractRow($row, $addressType.'_address', $exclude);
        $orderAddress = null;
        if (!count($row)) return null;
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $this->validateRow($entityType, $row);
        $row = $this->castRow($entityType, $row);
        foreach ($order->getAddressesCollection() as $address) {
            if (($address->getAddressType() == $addressType) && !$address->isDeleted()) {
                $orderAddress = $address; break;
            }
        }
        if (!$orderAddress) $orderAddress = Mage::getModel('sales/order_address');
        $orderAddress->setAddressType($addressType);
        if (!empty($row['name']) && empty($row['firstname']) && empty($row['lastname'])) {
            $name = $this->parseAddressName($row['name']);
            if (count($name)) {
                if (!empty($name['firstname'])) $row['firstname'] = $name['firstname'];
                if (!empty($name['middlename'])) $row['middlename'] = $name['middlename'];
                if (!empty($name['lastname'])) $row['lastname'] = $name['lastname'];
            }
        }
        if (!empty($row['country_id']) && !empty($row['region_id'])) {
            $region = $this->getRegion($row['country_id'], $row['region_id']);
            if ($region) $row['region'] = $region->getName();
        }
        $keys = array(
            'prefix', 'firstname', 'middlename', 'lastname', 'suffix', 'country_id', 'region_id', 'region', 
            'fax', 'postcode', 'street', 'city', 'telephone', 'company', 
        );
        if (!$orderAddress->getId()) {
            $orderAddress->setOrder($order);
            $orderAddress->setCustomerId($order->getCustomerId());
            if (
                $order->getCustomer() && !empty($row['country_id']) && !empty($row['region']) && 
                !empty($row['city']) && !empty($row['street']) && !empty($row['telephone']) && 
                !empty($row['firstname']) && !empty($row['lastname'])
            ) {
                $customer = $order->getCustomer();
                if ($customer) {
                    $customerAddress = null;
                    $collection = Mage::getResourceModel('customer/address_collection');
                    $collection->setCustomerFilter($customer);
                    foreach ($keys as $key) {
                        if (!empty($row[$key])) $collection->addAttributeToFilter($key, $row[$key]);
                    }
                    foreach ($collection as $address) { $customerAddress = $address; break; }
                    if ($customerAddress) $orderAddress->setCustomerAddressId($customerAddress->getId());
                }
            }
        }
        array_push($keys, 'email');
        $this->copyData($orderAddress, $row, $keys);
        return $orderAddress;
    }
    /**
     * Prepare shipping address row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Sales_Model_Order_Address
     */
    protected function prepareShippingAddress($order, $row)
    {
        $shippingAddress = $this->prepareAddress($order, $row, 'shipping', array('shipping_address_id'));
        if ($shippingAddress) $order->setShippingAddress($shippingAddress);
        return $shippingAddress;
    }
    /**
     * Prepare billing address row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @return Mage_Sales_Model_Order_Address
     */
    protected function prepareBillingAddress($order, $row)
    {
        $billingAddress = $this->prepareAddress($order, $row, 'billing', array('billing_address_id'));
        if ($billingAddress) $order->setBillingAddress($billingAddress);
        return $billingAddress;
    }
    /**
     * Get items rows indexes
     * 
     * @param array $row
     * @return array
     */
    protected function getItemsRowsIndexes($row)
    {
        $indexes = array();
        foreach ($row as $key => $value) {
            $prefix = $this->getItemPrefix();
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $parts = explode('_', $key);
                if (count($parts) > 1) {
                    $index = $parts[1];
                    if (((int) $index == $index) && !in_array($index, $indexes)) {
                        array_push($indexes, $index);
                    }
                }
            }
        }
        return $indexes;
    }
    /**
     * Get product by SKU
     * 
     * @param Mage_Core_Model_Store $store
     * @param string $sku
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductBySKU($store, $sku)
    {
        $product = Mage::getModel('catalog/product');
        $productId = $product->getIdBySku($sku);
        if ($productId) $product->setStore($store)->setStoreId($store->getId())->load($productId);
        if ($product->getId()) return $product;
        else return null;
    }
    /**
     * Get product by item row
     * 
     * @param Mage_Core_Model_Store $store
     * @param array $row
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductByItemRow($store, $row)
    {
        $helper = $this->getHelper();
        if (empty($row['sku'])) {
            $message = $helper->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product = $this->getProductBySKU($store, $row['sku']);
        if (!$product) {
            $message = $helper->__('Skipping import row, product not found.');
            Mage::throwException($message);
        }
        return $product;
    }
    /**
     * Prepare bundle item
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $parentItem
     * @param Mage_Sales_Model_Order_Item $item
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function prepareBundleItem($order, $parentItem, $item)
    {
        $helper = $this->getHelper();
        $product = $item->getProduct();
        $parentProduct = $parentItem->getProduct();
        $parentProductType = $parentProduct->getTypeInstance();
        $childrenIds = $parentProductType->getChildrenIds($parentProduct->getId());
        $optionId = null;
        if (count($childrenIds)) {
            foreach ($childrenIds as $_optionId => $ids) {
                if (count($ids)) {
                    foreach ($ids as $id) { if ($product->getId() == $id) { $optionId = $_optionId; break; } }
                }
            }
        }
        if (!$optionId) {
            $message = $helper->__('Skipping import row, invalid bundle child product.');
            Mage::throwException($message);
        }
        $option = null;
        $options = $parentProductType->getOptions($parentProduct);
        foreach ($options as $_option) {
            if ($_option->getId() == $optionId) { $option = $_option; break; }
        }
        if (!$option) {
            $message = $helper->__('Skipping import row, bundle option not found.');
            Mage::throwException($message);
        }
        $selection = null;
        $selections = $parentProductType->getSelectionsCollection(array($optionId), $parentProduct);
        if (count($selections)) {
            foreach ($selections as $_selection) {
                if ($_selection->getProductId() == $product->getId()) {
                    $selection = $_selection; break;
                }
            }
        }
        $parentOptions = $parentItem->getProductOptions();
        $options = $item->getProductOptions();
        $bundleOption = (isset($parentOptions['info_buyRequest']) && isset($parentOptions['info_buyRequest']['bundle_option'])) ? 
            $parentOptions['info_buyRequest']['bundle_option'] :array();
        if (isset($bundleOption[$optionId])) {
            if ($option->isMultiSelection()) {
                if (!in_array($selection->getSelectionId(), $bundleOption[$optionId])) {
                    array_push($bundleOption[$optionId], $selection->getSelectionId());
                }
            }
        } else {
            if ($option->isMultiSelection()) $bundleOption[$optionId] = array($selection->getSelectionId());
            else $bundleOption[$optionId] = $selection->getSelectionId();
        }
        $parentOptions['info_buyRequest']['bundle_option'] = $bundleOption;
        $options['info_buyRequest']['bundle_option'] = $bundleOption;
        $parentItem->setProductOptions($parentOptions);
        $item->setProductOptions($options);
        return $this;
    }
    /**
     * Prepare configurable item
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $parentItem
     * @param Mage_Sales_Model_Order_Item $item
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function prepareConfigurableItem($order, $parentItem, $item)
    {
        $helper = $this->getHelper();
        $product = $item->getProduct();
        $parentProduct = $parentItem->getProduct();
        $parentProductType = $parentProduct->getTypeInstance();
        $usedProductIds = $parentProductType->getUsedProductIds($parentProduct);
        if (!in_array($product->getId(), $usedProductIds)) {
            $message = $helper->__('Skipping import row, invalid configurable child product.');
            Mage::throwException($message);
        }
        $attributes = array();
        $attributesIds = $parentProductType->getUsedProductAttributeIds($parentProduct);
        if (count($attributesIds)) {
            foreach ($attributesIds as $attributeId) {
                $attribute = $parentProductType->getAttributeById($attributeId, $product);
                if ($attribute) {
                    $value = $product->getData($attribute->getAttributeCode());
                    if (!empty($value)) {
                        $attributes[$attributeId] = $value;
                    }
                }
            }
        }
        if (!count($attributes)) {
            $message = $helper->__('Skipping import row, configurable product options not found.');
            Mage::throwException($message);
        }
        $parentOptions = $parentItem->getProductOptions();
        $parentOptions['info_buyRequest']['super_attribute'] = $attributes;
        $parentOptions['simple_name'] = $item->getName();
        $parentOptions['simple_sku'] = $item->getSku();
        $parentItem->setProductOptions($parentOptions);
        $parentItem->setSku($item->getSku());
        $options = $item->getProductOptions();
        $options['info_buyRequest']['super_attribute'] = $attributes;
        $item->setProductOptions($options);
        return $this;
    }
    /**
     * Prepare item
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @param string $index
     * @param array $exclude
     * @return Mage_Sales_Model_Order_Item
     */
    protected function prepareItem($order, $row, $index, $exclude = array())
    {
        $entityType = 'sales_order_item';
        $baseCurrencyCode = (!empty($row['base_currency_code'])) ? $row['base_currency_code'] : null;
        $orderCurrencyCode = (!empty($row['order_currency_code'])) ? $row['order_currency_code'] : null;
        $store = $order->getStore();
        $baseCurrency = $order->getData('base_currency');
        $orderCurrency = $order->getData('order_currency');
        $row = $this->extractRow($row, 'item_'.$index, $exclude);
        if (empty($row['sku'])) return null;
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $this->validateRow($entityType, $row);
        $row = $this->castRow($entityType, $row);
        $product = $this->getProductByItemRow($store, $row);
        if (!$product) return null;
        $orderItem = null;
        foreach ($order->getAllItems() as $item) { if ($item->getProductId() == $product->getId()) { $orderItem = $item; break; } }
        if (!$orderItem) $orderItem = Mage::getModel('sales/order_item');
        $orderItem->setBaseCurrencyCode($order->getBaseCurrencyCode());
        $orderItem->setOrderCurrencyCode($order->getOrderCurrencyCode());
        $row['base_currency_code'] = $baseCurrencyCode;
        $row['order_currency_code'] = $orderCurrencyCode;
        if (!$orderItem->getId()) {
            $orderItem->setOrder($order);
            $orderItem->setStoreId($store->getId());
            $orderItem->setProduct($product);
            $orderItem->setProductId($product->getId());
            $orderItem->setProductType($product->getTypeId());
            $options = $product->getTypeInstance(true)->getOrderOptions($product);
            $orderItem->setProductOptions($options);
            $orderItem->setName($product->getName());
            $orderItem->setSku($product->getSku());
            $orderItem->setIsVirtual(($product->isVirtual() ? 1 : 0));
            $orderItem->setIsQtyDecimal(($product->getStockItem()->getIsQtyDecimal() ? 1 : 0));
            $orderItem->setIsNominal((($product->getIsRecurring() == '1') ? 1 : 0));
            if (!empty($row['parent_sku'])) {
                $parentProduct = $this->getProductBySKU($order->getStore(), $row['parent_sku']);
                if ($parentProduct) {
                    foreach ($order->getAllItems() as $parentOrderItem) {
                        if ($parentOrderItem->getProductId() == $parentProduct->getId()) {
                            $parentOrderItem->setProduct($parentProduct);
                            $orderItem->setParentItem($parentOrderItem); 
                            if ($parentOrderItem->getProductType() == 'configurable') {
                                $this->prepareConfigurableItem($order, $parentOrderItem, $orderItem);
                            } else if ($parentOrderItem->getProductType() == 'bundle') {
                                $this->prepareBundleItem($order, $parentOrderItem, $orderItem);
                            }
                            break;
                        }
                    }
                }
            }
        }
        if (!$orderItem->hasWeight()) $orderItem->setWeight($product->getWeight());
        if (!$orderItem->hasRowWeight()) {
            $qty = $orderItem->getQtyOrdered();
            $weight = $orderItem->getWeight();
            $rowWeight = $qty * $weight;
            $orderItem->setRowWeight(($orderItem->getFreeShipping()) ? 0 : $rowWeight);
        }
        if (!$orderItem->getBaseCost()) $orderItem->setBaseCost($product->getCost());
        $amountsKeys = array(
            'cost', 'price', 'original_price', 'tax_amount', 'tax_invoiced', 'discount_amount', 'discount_invoiced', 'amount_refunded', 
            'row_total', 'row_invoiced', 'tax_before_discount', 'weee_tax_applied_amount', 'weee_tax_applied_row_amount', 'weee_tax_row_disposition', 
            'weee_tax_disposition', 'hidden_tax_amount', 'hidden_tax_invoiced', 'hidden_tax_refunded', 'price_incl_tax', 'row_total_incl_tax', 
            'hidden_tax_canceled', 'tax_canceled', 'tax_refunded', 
        );
        foreach ($amountsKeys as $amountKey) { $row = $this->prepareRowAmount($orderItem, $row, $amountKey, $baseCurrency, $orderCurrency); }
        $copyKeys = array(
            'weight', 'row_weight', 'free_shipping', 'no_discount', 'applied_rule_ids', 'weee_tax_applied', 
            'qty_ordered', 'qty_backordered', 'qty_canceled', 'qty_invoiced', 'qty_refunded', 'qty_shipped', 
        );
        foreach ($amountsKeys as $amountKey) { array_push($copyKeys, $amountKey); array_push($copyKeys, 'base_'.$amountKey); }
        $this->copyData($orderItem, $row, $copyKeys);
        $options = $orderItem->getProductOptions();
        $options['info_buyRequest']['qty'] = $orderItem->getQtyOrdered() * 1;
        $options['info_buyRequest']['product'] = $product->getId();
        $orderItem->setProductOptions($options);
        $order->addItem($orderItem);
        return $orderItem;
    }
    /**
     * Prepare payment
     * 
     * @param Mage_Sales_Model_Order $order
     * @param array $row
     * @param string $index
     * @param array $exclude
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function preparePayment($order,$row, $exclude = array())
    {
        $entityType = 'sales_order_payment';
        $baseCurrencyCode = (!empty($row['base_currency_code'])) ? $row['base_currency_code'] : null;
        $orderCurrencyCode = (!empty($row['order_currency_code'])) ? $row['order_currency_code'] : null;
        $store = $order->getStore();
        $baseCurrency = $order->getData('base_currency');
        $orderCurrency = $order->getData('order_currency');
        $row = $this->extractRow($row, 'payment', $exclude);
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $this->validateRow($entityType, $row);
        $row = $this->castRow($entityType, $row);
        $orderPayments = $order->getAllPayments();
        if (isset($orderPayments[0])) $orderPayment = $orderPayments[0];    
        else $orderPayment = Mage::getModel('sales/order_payment');
        $orderPayment->setBaseCurrencyCode($order->getBaseCurrencyCode());
        $orderPayment->setOrderCurrencyCode($order->getOrderCurrencyCode());
        $row['base_currency_code'] = $baseCurrencyCode;
        $row['order_currency_code'] = $orderCurrencyCode;
        if (!$orderPayment->getId()) $orderPayment->setOrder($order);
        $amountsKeys = array(
            'shipping_amount', 'shipping_captured', 'shipping_refunded', 'amount_authorized', 'amount_paid', 'amount_paid_online', 
            'amount_refunded', 'amount_refunded_online', 'amount_ordered', 'amount_canceled', 'amount', 
        );
        foreach ($amountsKeys as $amountKey) { $row = $this->prepareRowAmount($orderPayment, $row, $amountKey, $baseCurrency, $orderCurrency); }
        $copyKeys = array(
            'cc_trans_id', 'cc_status', 'cc_status_description', 'cc_cid_status', 'cc_avs_status', 'cc_owner', 'cc_type', 'cc_exp_year', 
            'cc_exp_month', 'cc_last4', 'cc_secure_verify', 'cc_ss_start_year', 'cc_ss_start_month', 'cc_ss_issue', 'cc_approval', 
            'cc_number_enc', 'cc_raw_request', 'cc_debug_request_body', 'cc_raw_response', 'cc_debug_response_body', 
            'cc_debug_response_serialized', 'method', 'address_status', 'additional_data', 'additional_information', 'protection_eligibility', 
            'last_trans_id', 'account_status', 'echeck_type', 'echeck_bank_name', 'echeck_account_type', 'echeck_account_name', 
            'echeck_routing_number', 'ideal_issuer_id', 'ideal_issuer_title', 'ideal_transaction_checked', 'paybox_request_number', 
            'paybox_question_number', 'flo2cash_account_id', 'cybersource_token', 'po_number', 'anet_trans_method', 
        );
        foreach ($amountsKeys as $amountKey) { array_push($copyKeys, $amountKey); array_push($copyKeys, 'base_'.$amountKey); }
        $this->copyData($orderPayment, $row, $copyKeys);
        $order->setPayment($orderPayment);
        return $orderPayment;
    }
    /**
     * Save row
     * 
     * @param unknown_type $row
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    public function saveRow($row)
    {
        $helper = $this->getHelper();
        $orderEntityType = 'sales_order';
        $row = $this->unsetRowIgnoreFields($orderEntityType, $row);
        $row = $this->filterRow($row);
        $this->validateRow($orderEntityType, $row);
        $row = $this->castRow($orderEntityType, $row);
        $order = $this->getOrderByRow($row);
        $this->prepareStore($order, $row);
        $this->prepareStatus($order, $row);
        $this->prepareShipping($order, $row);
        $this->prepareCustomer($order, $row);
        $this->prepareAmounts($order, $row);
        $this->copyData($order, $row, array(
            'real_order_id', 'coupon_code', 'can_ship_partially', 'can_ship_partially_item', 'forced_do_shipment_with_invoice', 
            'paypal_ipn_customer_notified', 'is_hold', 'is_multi_payment', 'email_sent', 'payment_authorization_amount', 
            'payment_authorization_expiration', 'weight', 'total_item_count', 'total_qty_ordered', 'tax_percent', 
            'remote_ip', 'x_forwarded_for', 'applied_rule_ids', 'ext_order_id', 'tracking_numbers', 
        ));
        $this->prepareShippingAddress($order, $row);
        $this->prepareBillingAddress($order, $row);
        $indexes = $this->getItemsRowsIndexes($row);
        foreach ($indexes as $index) $this->prepareItem($order, $row, $index, array());
        $this->preparePayment($order, $row, array());
        if (!$order->getShippingAddress()) $order->setIsVirtual(1);
        $order->save();
        return $this;
    }
}