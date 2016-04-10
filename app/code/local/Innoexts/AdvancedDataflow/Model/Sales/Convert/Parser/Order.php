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
 * Order parser
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Sales_Convert_Parser_Order extends Mage_Eav_Model_Convert_Parser_Abstract
{
    /**
     * System fields
     * 
     * @var array
     */
    protected $_systemFields;
    /**
     * Required fields
     * 
     * @var array
     */
    protected $_requiredFields;
    /**
     * Ignore fields
     * 
     * @var array
     */
    protected $_ignoreFields;
    /**
     * Attributes
     * 
     * @var array
     */
    protected $_attributes;
    /**
     * Stores
     * 
     * @var array
     */
    protected $_stores;
    /**
     * Countries
     * 
     * @var array
     */
    protected $_countries;
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
        $this->setVar('entity_type', 'sales/order');
        if (!Mage::registry('Object_Cache_Order')) $this->setOrder(Mage::getModel('sales/order'));
        $this->initializeFields();
    }
    /**
     * Get helper
     * 
     * @return Innoexts_AdvancedDataflow_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('advanceddataflow');
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
     * Retrieve stores
     *
     * @return array
     */
    protected function getStores()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
        }
        return $this->_stores;
    }
    /**
     * Retrieve store by identifier
     * 
     * @param string $id
     * @return Mage_Core_Model_Store
     */
    protected function getStoreById($id)
    {
        $store = null;
        $stores = $this->getStores();
        foreach ($stores as $_store) {
            if ($id == $_store->getId()) { $store = $_store; break; }
        }
        return $store;
    }
    /**
     * Initialize entity type field
     * 
     * @param string $entityType
     * @param string $fieldName
     * @param Mage_Core_Model_Config_Element $fieldNode
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function initializeEntityTypeField($entityType, $fieldName, $fieldNode)
    {
        if ($fieldNode->is('system')) {
            $this->_systemFields[$entityType][] = $fieldName;
        }
        if ($fieldNode->is('required')) {
            $this->_requiredFields[$entityType][] = $fieldName;
        }
        if ($fieldNode->is('ignore')) {
            $this->_ignoreFields[$entityType][] = $fieldName;
        }
        return $this;
    }
    /**
     * Check if field is system
     * 
     * @param string $entityType
     * @param string $fieldName
     * @return boolean
     */
    protected function isFieldSystem($entityType, $fieldName)
    {
        return (isset($this->_systemFields[$entityType]) && in_array($fieldName, $this->_systemFields[$entityType])) ? true : false;
    }
    /**
     * Check if field is require
     * 
     * @param string $entityType
     * @param string $fieldName
     * @return boolean
     */
    protected function isFieldRequired($entityType, $fieldName)
    {
        return (isset($this->_requiredFields[$entityType]) && in_array($fieldName, $this->_requiredFields[$entityType])) ? true : false;
    }
    /**
     * Check if field is ignore
     * 
     * @param string $entityType
     * @param string $fieldName
     * @return boolean
     */
    protected function isFieldIgnore($entityType, $fieldName)
    {
        return (isset($this->_ignoreFields[$entityType]) && in_array($fieldName, $this->_ignoreFields[$entityType])) ? true : false;
    }
    /**
     * Initialize entity type fields
     * 
     * @param string $entityType
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function initializeEntityTypeFields($entityType)
    {
        foreach (Mage::getConfig()->getFieldset($entityType.'_dataflow', 'admin') as $fieldName => $fieldNode) {
            $this->initializeEntityTypeField($entityType, $fieldName, $fieldNode);
        }
        return $this;
    }
    /**
     * Initialize fields
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Adapter_Order
     */
    protected function initializeFields()
    {
        if (is_null($this->_systemFields)) $this->_systemFields = array();
        if (is_null($this->_requiredFields)) $this->_requiredFields = array();
        if (is_null($this->_ignoreFields)) $this->_ignoreFields = array();
        $this->initializeEntityTypeFields('sales_order');
        $this->initializeEntityTypeFields('sales_order_address');
        $this->initializeEntityTypeFields('sales_order_item');
        $this->initializeEntityTypeFields('sales_order_payment');
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
     * Retrieve attributes
     * 
     * @param string $entityType
     * @return array
     */
    protected function getAttributes($entityType)
    {
        if (is_null($this->_attributes)) $this->_attributes = array();
        if (!isset($this->_attributes[$entityType])) {
            foreach ($this->getAttributeCollection($entityType) as $attribute) {
                $this->_attributes[$entityType][$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $this->_attributes[$entityType];
    }
    /**
     * Get attribute
     * 
     * @param string $entityType
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function getAttribute($entityType, $code)
    {
        $attributes = $this->getAttributes($entityType);
        if (isset($attributes[$code])) return $attributes[$code];
        else return null;
    }
    /**
     * Get attribute type for upcoming validation.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected static function getAttributeType(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->usesSource()) return 'select';
        elseif ($attribute->isStatic()) return $attribute->getFrontendInput() == 'date' ? 'datetime' : 'varchar';
        else return $attribute->getBackendType();
    }
    /**
     * Get country
     * 
     * @param string $countryId
     * @return Mage_Directory_Model_Country
     */
    protected function getCountry($countryId)
    {
        if (is_null($this->_countries)) {
            $this->_countries = array();
            $countries = Mage::getResourceModel('directory/country_collection');
            foreach ($countries as $country) {
                $this->_countries[$country->getId()] = $country;
            }
        }
        if (isset($this->_countries[$countryId])) return $this->_countries[$countryId];
        else return null;
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
            $regions = Mage::getResourceModel('directory/region_collection');
            foreach ($regions as $region) {
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
     * Unparse address name
     * 
     * @param array $array
     * @return string
     */
    protected function unparseAddressName($array)
    {
        $name = null;
        $pieces = array();
        if (!empty($array['prefix'])) array_push($pieces, $array['prefix']);
        if (!empty($array['firstname'])) array_push($pieces, $array['firstname']);
        if (!empty($array['middlename'])) array_push($pieces, $array['middlename']);
        if (!empty($array['lastname'])) array_push($pieces, $array['lastname']);
        if (!empty($array['suffix'])) array_push($pieces, $array['suffix']);
        $name = implode(' ', $pieces);
        return $name;
    }
    /**
     * Entity to row
     * 
     * @param Mage_Core_Model_Abstract $entity
     * @param string $entityType
     * @param string $prefix
     * @param array $useOptionTitleFields
     * @param array $exclude
     * @return array
     */
    protected function entityToRow($entity, $entityType, $prefix, $useOptionTitleFields = array(), $exclude = array())
    {
        $helper = $this->getHelper();
        $array = array();
        if ($entity) {
            foreach ($entity->getData() as  $field => $value) {
                if ($this->isFieldSystem($entityType, $field) || is_object($value)) continue;
                if (in_array($field, $exclude)) continue;
                $attribute = $this->getAttribute($entityType, $field);
                if (!$attribute) continue;
                if ($attribute->usesSource() && in_array($field, $useOptionTitleFields)) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($option) $value = $option;
                    unset($option);
                } elseif (is_array($value)) continue;
                $type = $this->getAttributeType($attribute);
                if ($type == 'decimal') {
                    if (is_null($value) || ($value === '') || ($value === false)) $value = null;
                    else $value = (float) $value;
                } elseif ($type == 'int') {
                    if (is_null($value) || ($value === '') || ($value === false)) $value = null;
                    else $value = (int) $value;
                }
                $array[$prefix.$field] = $value;
            }
        }
        return $array;
    }
    /**
     * Order to row
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function orderToRow($order)
    {
        $row = $this->entityToRow($order, 'sales_order', '');
        if (!empty($row['created_at'])) $row['created_at_timestamp'] = strtotime($row['created_at']);
        if (!empty($row['updated_at'])) $row['updated_at_timestamp'] = strtotime($row['updated_at']);
        $row['customer_name'] = $this->unparseAddressName(array(
            'prefix' => ((!empty($row['customer_prefix'])) ? $row['customer_prefix'] : null), 
            'firstname' => ((!empty($row['customer_firstname'])) ? $row['customer_firstname'] : null), 
            'middlename' => ((!empty($row['customer_middlename'])) ? $row['customer_middlename'] : null), 
            'lastname' => ((!empty($row['customer_lastname'])) ? $row['customer_lastname'] : null), 
            'suffix' => ((!empty($row['customer_suffix'])) ? $row['customer_suffix'] : null), 
        ));
        return $row;
    }
    /**
     * Order address to row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @param string $prefix
     * @return array
     */
    protected function orderAddressToRow($order, $orderAddress, $prefix)
    {
        $row = $this->entityToRow($orderAddress, 'sales_order_address', $prefix);
        if (!empty($row[$prefix.'country_id'])) {
            $country = $this->getCountry($row[$prefix.'country_id']);
            if ($country) {
                $row[$prefix.'country_iso2'] = $country->getIso2Code();
                $row[$prefix.'country_iso3'] = $country->getIso3Code();
                $row[$prefix.'country'] = $country->getName();
            }
            if (!empty($row[$prefix.'region_id'])) {
                $region = $this->getRegion($row[$prefix.'country_id'], $row[$prefix.'region_id']);
                if ($region) {
                    $row[$prefix.'region_iso2'] = $region->getCode();
                    $row[$prefix.'region'] = $region->getName();
                }
            }
        }
        if (count($row)) {
            $row[$prefix.'name'] = $this->unparseAddressName(array(
                'prefix' => ((!empty($row[$prefix.'prefix'])) ? $row[$prefix.'prefix'] : null), 
                'firstname' => ((!empty($row[$prefix.'firstname'])) ? $row[$prefix.'firstname'] : null), 
                'middlename' => ((!empty($row[$prefix.'middlename'])) ? $row[$prefix.'middlename'] : null), 
                'lastname' => ((!empty($row[$prefix.'lastname'])) ? $row[$prefix.'lastname'] : null), 
                'suffix' => ((!empty($row[$prefix.'suffix'])) ? $row[$prefix.'suffix'] : null), 
            ));
        }
        return $row;
    }
    /**
     * Order shipping address to row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @return array
     */
    protected function orderShippingAddressToRow($order, $orderAddress)
    {
        return $this->orderAddressToRow($order, $orderAddress, $this->getShippingAddressPrefix(true));
    }
    /**
     * Order billing address to row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @return array
     */
    protected function orderBillingAddressToRow($order, $orderAddress)
    {
        return $this->orderAddressToRow($order, $orderAddress, $this->getBillingAddressPrefix(true));
    }
    /**
     * Get product by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductByOrderItem($orderItem)
    {
        $storeId = (int) $orderItem->getStoreId();
        $productId = (int) $orderItem->getProductId();
        $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
        if ($product->getId()) {
            return $product;
        } else {
            return null;
        }
    }
    /**
     * Get product sku by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductSkuByOrderItem($orderItem)
    {
        $product = $this->getProductByOrderItem($orderItem);
        if ($product) {
            return $product->getSku();
        } else {
            return null;
        }
    }
    /**
     * Get parent product by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Catalog_Model_Product
     */
    protected function getParentProductByOrderItem($orderItem)
    {
        $parentOrderItem = $orderItem->getParentItem();
        if ($parentOrderItem) {
            return $this->getProductByOrderItem($parentOrderItem);
        } else {
            return null;
        }
    }
    /**
     * Get parent product sku by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return string
     */
    protected function getParentProductSkuByOrderItem($orderItem)
    {
        $product = $this->getParentProductByOrderItem($orderItem);
        if ($product) {
            return $product->getSku();
        } else {
            return null;
        }
    }
    /**
     * Get children products by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return array of Mage_Catalog_Model_Product
     */
    protected function getChildrenProductsByOrderItem($orderItem)
    {
        $products = array();
        $childrenOrderItems = $orderItem->getChildrenItems();
        if (count($childrenOrderItems)) {
            foreach ($childrenOrderItems as $childOrderItem) {
                $product = $this->getProductByOrderItem($childOrderItem);
                if ($product) {
                    $products[$product->getId()] = $product;
                }
            }
        }
        return $products;
    }
    /**
     * Get children products skus by order item
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    protected function getChildrenProductsSkusByOrderItem($orderItem)
    {
        $skus = array();
        $products = $this->getChildrenProductsByOrderItem($orderItem);
        if (count($products)) {
            foreach ($products as $product) {
                array_push($skus, $product->getSku());
            }
        }
        return $skus;
    }
    /**
     * Order item to row
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param int $index
     * @return array
     */
    protected function orderItemToRow($order, $orderItem, $index)
    {
        $prefix = $this->getItemPrefix(true).($index + 1).'_';
        $row = $this->entityToRow($orderItem, 'sales_order_item', $prefix);
        $sku = $this->getProductSkuByOrderItem($orderItem);
        if ($sku) {
            $row[$prefix.'sku'] = $sku;
            $parentSku = $this->getParentProductSkuByOrderItem($orderItem);
            if ($parentSku) {
                $row[$prefix.'parent_sku'] = $parentSku;
            }
            $childrenSkus = $this->getChildrenProductsSkusByOrderItem($orderItem);
            if (count($childrenSkus)) {
                $row[$prefix.'children_skus'] = implode(',', $childrenSkus);
            }
        }
        return $row;
    }
    /**
     * Order payment to array
     * 
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Payment $orderPayment
     * @return array
     */
    protected function orderPaymentToRow($order, $orderPayment)
    {
        $row = $this->entityToRow($orderPayment, 'sales_order_payment', $this->getPaymentPrefix(true));
        return $row;
    }
    /**
     * Unparse
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Parser_Order
     */
    public function unparse()
    {
        $helper = $this->getHelper();
        $entityIds = $this->getData();
        foreach ($entityIds as $i => $entityId) {
            $order = $this->getOrder()->reset()->load($entityId);
            $position = $helper->__('Line %d, ID: %s', ($i+1), $order->getIncrementId());
            $this->setPosition($position);
            $row = array();
            $store = $this->getStoreById($order->getData('store_id'));
            if (!$store) $store = $this->getStoreById(0);
            $row['store'] = $store->getCode();
            $row = array_merge(
                $row, $this->orderToRow($order), $this->orderShippingAddressToRow($order, $order->getShippingAddress()), 
                $this->orderBillingAddressToRow($order, $order->getBillingAddress())
            );
            $orderItems = $order->getAllItems();
            if (count($orderItems)) {
                foreach ($orderItems as $index => $orderItem) {
                    $row = array_merge($row, $this->orderItemToRow($order, $orderItem, $index));
                }
            }
            $orderPayments = $order->getAllPayments();
            if (count($orderPayments)) {
                $orderPayment = $orderPayments[0];
                $row = array_merge($row, $this->orderPaymentToRow($order, $orderPayment));
            }
            ksort($row);
            $batchExport = $this->getBatchExportModel()->setId(null)
                ->setBatchId($this->getBatchModel()->getId())->setBatchData($row)
                ->setStatus(1)->save();
        }
        return $this;
    }
    /**
     * Parse
     * 
     * @return Innoexts_AdvancedDataflow_Model_Sales_Convert_Parser_Order
     */
    public function parse()
    {
        return $this;
    }
    /**
     * Get external attributes
     * 
     * @return array
     */
    public function getExternalAttributes()
    {
        $internal = array('store_id', 'entity_id');
        $orderAttributes = Mage::getResourceModel('advanceddataflow/sales_order_attribute_collection');
        $attributes = array();
        foreach ($orderAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput() == 'hidden') continue;
            $attributes[$code] = $code;
        }
        return $attributes;
    }
}