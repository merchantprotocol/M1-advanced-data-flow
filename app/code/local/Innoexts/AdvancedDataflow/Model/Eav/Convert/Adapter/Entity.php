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
 * Entity adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity extends Mage_Eav_Model_Convert_Adapter_Entity
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
     * Websites
     * 
     * @var array
     */
    protected $_websites;
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
     * Constructor
     */
    public function __construct()
    {
        $this->initializeFields();
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
     * Initialize entity type field
     * 
     * @param string $entityType
     * @param string $fieldName
     * @param Mage_Core_Model_Config_Element $fieldNode
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
     */
    protected function initializeEntityTypeField($entityType, $fieldName, $fieldNode)
    {
        if ($fieldNode->is('system'))     $this->_systemFields[$entityType][] = $fieldName;
        if ($fieldNode->is('required'))   $this->_requiredFields[$entityType][] = $fieldName;
        if ($fieldNode->is('ignore'))     $this->_ignoreFields[$entityType][] = $fieldName;
        return $this;
    }
    /**
     * Initialize entity type fields
     * 
     * @param string $entityType
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
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
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
     */
    protected function initializeFields()
    {
        if (is_null($this->_systemFields)) $this->_systemFields = array();
        if (is_null($this->_requiredFields)) $this->_requiredFields = array();
        if (is_null($this->_ignoreFields)) $this->_ignoreFields = array();
        return $this;
    }
    /**
     * Unset row ignore fields
     * 
     * @param string $entityType
     * @param array $row
     * @return array
     */
    protected function unsetRowIgnoreFields($entityType, $row)
    {
        if (isset($this->_ignoreFields[$entityType])) {
            foreach ($this->_ignoreFields[$entityType] as $field) {
                if (isset($row[$field])) unset($row[$field]);
            }
        }
        return $row;
    }
    /**
     * Filter row values
     * 
     * @param array $row
     * @return array
     */
    protected function filterRow($row)
    {
        $string = $this->getStringHelper();
        foreach ($row as $field => $value) { $row[$field] = $string->cleanString(trim($value)); }
        return $row;
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
     * Get option value by value
     * 
     * @param array $options
     * @param mixed $value
     * @return mixed
     */
    protected function getOptionValueByValue($options, $value)
    {
        $optionValue = null;
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                $optionValue = $this->getOptionValueByValue($option['value'], $value);
                if (!is_null($optionValue)) return $optionValue;
            } else {
                if (
                    (strtoupper($value) == strtoupper($option['value'])) || 
                    (isset($option['value2']) && strtoupper($value) == strtoupper($option['value2'])) || 
                    (strtoupper($value) == strtoupper($option['label']))
                ) { return $option['value']; }
            }
        }
        return $optionValue;
    }
    /**
     * Check if option value exists
     * 
     * @param array $options
     * @param mixed $value
     * @return boolean
     */
    protected function isOptionValueExists($options, $value)
    {
        $optionValue = $this->getOptionValueByValue($options, $value);
        return (!is_null($optionValue)) ? true : false;
    }
    /**
     * Validate row
     * 
     * @param string $entityType
     * @param array $row
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
     */
    protected function validateRow($entityType, $row, $validateRequired = true)
    {
        $helper = $this->getHelper();
        $string = $this->getStringHelper();
        if ($validateRequired) {
            if (isset($this->_requiredFields[$entityType])) {
                foreach ($this->_requiredFields[$entityType] as $field) {
                    if (empty($row[$field])) {
                        $message = $helper->__('Skip import row, required field "%s" for the new order is not defined.', $field);
                        Mage::throwException($message);
                    }
                }
            }
        }
        foreach ($row as $field => $value) {
            if (empty($row[$field])) continue;
            $attribute = $this->getAttribute($entityType, $field);
            if (!$attribute) continue;
            if (is_string($value) && (strtoupper($value) === 'NULL')) $value = null;
            $type = $this->getAttributeType($attribute);
            switch ($type) {
                case 'varchar': $isValid = ($string->strlen($value) < 256); break;
                case 'decimal': $isValid = (float) $value == $value; break;
                case 'int': $isValid = (int) $value == $value; break;
                case 'datetime': 
                    $isValid = strtotime($value) || preg_match('/^\d{2}.\d{2}.\d{2,4}(?:\s+\d{1,2}.\d{1,2}(?:.\d{1,2})?)?$/', $value); break;
                case 'text': $isValid = ($string->strlen($value) < 65536); break;
                case 'select': 
                    if (empty($value)) $isValid = true;
                    else {
                        $options = $attribute->getSource()->getAllOptions(false);
                        $isValid = $this->isOptionValueExists($options, $value);
                    }
                    break;
                default: $isValid = true; break;
            }
            if (!$isValid) {
                $message = $helper->__('Skip import row, field "%s" is not valid.', $field);
                Mage::throwException($message);
            }
        }
        return $this;
    }
    /**
     * Cast row values
     * 
     * @param string $entityType
     * @param array $row
     * @return array
     */
    protected function castRow($entityType, $row)
    {
        $helper = $this->getHelper();
        $string = $this->getStringHelper();
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        foreach ($row as $field => $value) {
            $attribute = $this->getAttribute($entityType, $field);
            if (!$attribute) continue;
            if (is_string($value) && (strtoupper($value) === 'NULL')) $value = null;
            $type = $this->getAttributeType($attribute);
            switch ($type) {
                case 'decimal': 
                    if (is_null($row[$field]) || ($row[$field] === '') || ($row[$field] === false)) $value = null;
                    else $value = (float) $value;
                    break;
                case 'int': 
                    if (is_null($row[$field]) || ($row[$field] === '') || ($row[$field] === false)) $value = null;
                    else $value = (int) $value;
                    break;
                case 'datetime': 
                    if (empty($row[$field])) $value = null;
                    else $value = gmstrftime($strftimeFormat, strtotime($value));
                    break;
                case 'select': 
                    $options = $attribute->getSource()->getAllOptions(false);
                    $value = $this->getOptionValueByValue($options, $value);
                    break;
                default: $value = strval($value); break;
            }
            $row[$field] = $value;
        }
        return $row;
    }
    /**
     * Copy data
     * 
     * @param Varien_Object $target
     * @param array $source
     * @param array $keys
     * @return Varien_Object
     */
    protected function copyData($target, $source, $keys)
    {
        foreach ($keys as $key) { if (isset($source[$key])) { $target->setData($key, $source[$key]); } }
        return $target;
    }
    /**
     * Get array elements starting with prefix
     * 
     * @param array $origionalRow
     * @param string $prefix
     * @param array $exclude
     * @return array
     */
    protected function extractRow($origionalRow, $prefix, $exclude = array())
    {
        $row = array();
        if (is_array($origionalRow) && count($origionalRow)) {
            foreach ($origionalRow as $key => $value) {
                if (!in_array($key, $exclude) && (substr($key, 0, strlen($prefix) + 1) == $prefix.'_')) {
                    $row[substr($key, strlen($prefix) + 1)] = $origionalRow[$key];
                }
            }
        }
        return $row;
    }
    /**
     * Get string helper
     * 
     * @return Mage_Core_Helper_String
     */
    protected function getStringHelper()
    {
        return Mage::helper('core/string');
    }
    /**
     * Retrieve attribute collection
     * 
     * @param string $entityType
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function getAttributeCollection($entityType)
    {
        return array();
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
     * Retrieve website
     * 
     * @param string $code
     * @return Mage_Core_Model_Website
     */
    protected function getWebsite($code)
    {
        if (is_null($this->_websites)) $this->_websites = Mage::app()->getWebsites(true, true);
        if (isset($this->_websites[$code])) return $this->_websites[$code];
        return false;
    }
    /**
     * Retrieve stores
     *
     * @return array
     */
    protected function getStores()
    {
        if (is_null($this->_stores)) { $this->_stores = Mage::app()->getStores(true, true); }
        return $this->_stores;
    }
    /**
     * Get default store code
     * 
     * @return string
     */
    protected function getDefaultStoreCode()
    {
        $store = $this->getVar('defaultStore', 'default');
        $stores = array_keys($this->getStores());
        if (in_array($store, $stores)) return $store;
        else return array_shift($stores);
    }
	/**
     * Retrieve store by code
     *
     * @param string $code
     * @return Mage_Core_Model_Store
     */
    protected function getStoreByCode($code)
    {
        $stores = $this->getStores();
        if (isset($this->_stores[$code])) return $this->_stores[$code];
        else return null;
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
        foreach ($stores as $_store) { if ($id == $_store->getId()) { $store = $_store; break; } }
        return $store;
    }
    /**
     * Get default store
     * 
     * @return Mage_Core_Model_Store
     */
    protected function getDefaultStore()
    {
        return $this->getStoreByCode($this->getDefaultStoreCode());
    }
    /**
     * Parse address name
     * 
     * @param string $name
     * @return array
     */
    protected function parseAddressName($name)
    {
        $parts = array();
        $_parts = explode(' ', $name);
        foreach ($_parts as $_part) {
            if (!empty($_part)) array_push($parts, $_part);
        }
        if (count($parts)) {
            $_parts = $parts;
            if (count($_parts) == 2) $parts = array('firstname' => $_parts[0], 'lastname' => $_parts[1]);
            else if (count($_parts) == 3) $parts = array('firstname' => $_parts[0], 'middlename' => $_parts[1], 'lastname' => $_parts[2]);
            else $parts = array();
        }
        return $parts;
    }
    /**
     * Parse
     * 
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
     */
    public function parse()
    {
        return $this;
    }
    /**
     * Save
     * 
     * @return Innoexts_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
     */
    public function save()
    {
        return $this;
    }
    /**
     * Debug
     * 
     * @param Varien_Object $object
     */    
    protected function _deb($object)
    {
        $array = array();
        foreach ($object->getData() as $key => $value) {
            if (!is_array($value) && !is_object($value) && !is_resource($value)) {
                $array[$key] = $value;
            }
        }
        echo var_export($array, true);
    }
}