<?php
/**
 * Mage Plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mage Plugins Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://mageplugins.net/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mageplugins@gmail.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to https://www.mageplugins.net for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2018 Mage Plugins Inc. and affiliates (https://mageplugins.net/)
 * @license    https://mageplugins.net/commercial-license/  Mage Plugins Commercial License (MPCL 1.0)
 */
 
/**
 * Customer adapter
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer extends MP_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
{
    /**
     * Customer groups
     * 
     * @var array
     */
    protected $_customerGroups;
    /**
     * Customer tax classes
     * 
     * @var array
     */
    protected $_taxClasses;
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
        $this->setVar('entity_type', 'customer/customer');
        if (!$this->hasCustomer()) $this->setCustomer(Mage::getModel('customer/customer'));
    }
    /**
     * Get address prefix
     * 
     * @param boolean $underscored
     * @return string
     */
    protected function getAddressPrefix($underscored = false)
    {
        return 'address'.(($underscored) ? '_' : '');
    }
    /**
     * Check if customer exists
     * 
     * @return boolean
     */
    public function hasCustomer()
    {
        return (Mage::registry('Object_Cache_Customer')) ? true : false;
    }
    /**
     * Set customer
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $id = Mage::objects()->save($customer);
        Mage::register('Object_Cache_Customer', $id);
        return $this;
    }
    /**
     * Get customer
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_Customer'));
    }
    /**
     * Initialize fields
     * 
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function initializeFields()
    {
        parent::initializeFields();
        $this->initializeEntityTypeFields('customer');
        $this->initializeEntityTypeFields('customer_address');
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
        if ($entityType == 'customer') return Mage::getResourceModel('customer/attribute_collection');
        else if ($entityType == 'customer_address') return Mage::getResourceModel('customer/address_attribute_collection');
        else return array();
    }
    /**
     * Retrieve customer tax classes
     *
     * @return array
     */
    protected function getTaxClasses()
    {
        if (is_null($this->_taxClasses)) {
            $this->_taxClasses = array();
            $collection = Mage::getModel('tax/class')->getCollection()->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
            foreach ($collection as $class) $this->_taxClasses[$class->getClassName()] = $class;
        }
        return $this->_taxClasses;
    }
    /**
     * Get customer tax class
     * 
     * @param string $name
     * @return Mage_Tax_Model_Class
     */
    protected function getTaxClass($name)
    {
        $classes = $this->getTaxClasses();
        if (isset($classes[$name])) return $classes[$name];
        else return null;
    }
    /**
     * Get customer tax class by class name
     * 
     * @param string $name
     * @return int
     */
    protected function getTaxClassIdByName($name)
    {
        $taxClass = $this->getTaxClass($name);
        if ($taxClass) return $taxClass->getId();
        else return null;
    }
    /**
     * Get default tax class
     * 
     * @return string
     */
    protected function getDefaultTaxClass()
    {
        return $this->getVar('defaultTaxClass', 'Default');
    }
    /**
     * Get default tax class identifier
     * 
     * @return int
     */
    protected function getDefaultTaxClassId()
    {
        return $this->getTaxClassIdByName($this->getDefaultTaxClass());
    }
    /**
     * Retrieve customer groups
     *
     * @return array
     */
    protected function getCustomerGroups()
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = array();
            $collection = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_id', array('gt' => 0));
            foreach ($collection as $group) $this->_customerGroups[$group->getCustomerGroupCode()] = $group;
        }
        return $this->_customerGroups;
    }
    /**
     * Append customer group
     * 
     * @param string $code
     * @return int
     */
    protected function appendCustomerGroup($code)
    {
        $customerGroup = Mage::getModel('customer/group');
        $customerGroup->setCode($code);
        $customerGroup->setTaxClassId($this->getDefaultTaxClassId());
        $customerGroup->save();
        $this->_customerGroups = null;
        return $customerGroup->getId();
    }
    /**
     * Get customer group
     * 
     * @param string $code
     * @return Mage_Customer_Model_Group
     */
    protected function getCustomerGroup($code)
    {
        $groups = $this->getCustomerGroups();
        if (isset($groups[$code])) return $groups[$code];
        else return null;
    }
    /**
     * Get customer group identifier by code
     * 
     * @param string $code
     * @return int
     */
    protected function getCustomerGroupIdByCode($code)
    {
        $customerGroup = $this->getCustomerGroup($code);
        if ($customerGroup) return $customerGroup->getId();
        else return null;
    }
    /**
     * Get and load customer by row
     * 
     * @param array $row
     * @return Mage_Customer_Model_Customer
     */
    protected function getCustomerByRow($row)
    {
        $helper = $this->getHelper();
        $customer = $this->getCustomer();
        $customer->reset();
        if (empty($row['website'])) {
            $message = $helper->__('Skipping import row, required field "%s" is not defined.', 'website');
            Mage::throwException($message);
        }
        $website = $this->getWebsite($row['website']);
        if ($website === false) {
            $message = $helper->__('Skipping import row, website "%s" field does not exist.', $row['website']);
            Mage::throwException($message);
        }
        $customer->setWebsiteId($website->getId());
        if (empty($row['real_customer_id']) && empty($row['email'])) {
            $message = Mage::helper('customer')->__('Skipping import row, customer identifier or email must be defined.');
            Mage::throwException($message);
        }
        if (!empty($row['real_customer_id'])) {
            $collection = $customer->getResourceCollection()->addAttributeToSelect('*')
                ->addAttributeToFilter('real_customer_id', $row['real_customer_id'])->setPage(1,1);
            foreach ($collection as $object) { $customer = $object; break; }
        } else $customer->loadByEmail($row['email']);
        $customer->setWebsiteId($website->getId());
        return $customer;
    }
    /**
     * Prepare group
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function prepareGroup($customer, $row)
    {
        $helper = $this->getHelper();
        $customerGroupId = (!empty($row['group'])) ? $this->getCustomerGroupIdByCode($row['group']) : null;
        if (!$customerGroupId && !empty($row['group'])) {
            $customerGroupCode = $row['group'];
            $customerGroupId = $this->appendCustomerGroup($customerGroupCode);
        }
        if (!$customer->getId()) {
            if (!$customerGroupId) {
                $value = isset($row['group']) ? $row['group'] : '';
                $message = $helper->__('Skipping import row, the value "%s" is not valid for the "%s" field.', $value, 'group');
                Mage::throwException($message);
            }
            $customer->setGroupId($customerGroupId);
        } elseif ($customerGroupId) $customer->setGroupId($customerGroupId);
        return $this;
    }
    /**
     * Prepare store
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function prepareStore($customer, $row)
    {
        if (!$customer->getId()) {
            if (empty($row['created_in']) || !$this->getStoreByCode($row['created_in'])) {
                $customer->setStoreId($this->getDefaultStore()->getId());
            } else {
                $customer->setStoreId($this->getStoreByCode($row['created_in'])->getId());
            }
        }
        return $this;
    }
    /**
     * Prepare password
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function preparePassword($customer, $row)
    {
        if (!$customer->getId()) {
            if (empty($row['password_hash'])) {
                $customer->setPasswordHash($customer->hashPassword($customer->generatePassword(8)));
            }
        }
        return $this;
    }
    /**
     * Prepare subscription
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function prepareSubscription($customer, $row)
    {
        if (isset($row['is_subscribed'])) {
            $customer->setData('is_subscribed', (($row['is_subscribed']) ? 1 : 0));
        }
        return $this;
    }
    /**
     * Prepare attributes
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function prepareAttributes($customer, $row)
    {
        $entityType = 'customer';
        foreach ($row as $field => $value) {
            $attribute = $this->getAttribute($entityType, $field);
            if (!$attribute) continue;
            $customer->setData($field, $value);
        }
        return $this;
    }
    /**
     * Get addresses rows indexes
     * 
     * @param array $row
     * @return array
     */
    protected function getAddressesRowsIndexes($row)
    {
        $string = $this->getStringHelper();
        $indexes = array();
        foreach ($row as $key => $value) {
            $prefix = $this->getAddressPrefix();
            if ($string->substr($key, 0, strlen($prefix)) == $prefix) {
                $parts = explode('_', $key);
                if (count($parts) > 1) {
                    $index = $parts[1];
                    if (((int) $index == $index) && !in_array($index, $indexes)) { array_push($indexes, $index); }
                }
            }
        }
        return $indexes;
    }
    /**
     * Prepare address attributes
     * 
     * @param Mage_Customer_Model_Address $customerAddress
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    protected function prepareAddressAttributes($customerAddress, $row)
    {
        $entityType = 'customer_address';
        foreach ($row as $field => $value) {
            $attribute = $this->getAttribute($entityType, $field);
            if (!$attribute) continue;
            $customerAddress->setDataUsingMethod($field, $value);
        }
        return $this;
    }
    /**
     * Retrieve region id by country code and region name (if exists)
     *
     * @param string $country
     * @param string $region
     * @return int
     */
    public function getRegionId($country, $regionName)
    {
        if (is_null($this->_regions)) {
            $this->_regions = array();
            $collection = Mage::getModel('directory/region')->getCollection();
            foreach ($collection as $region) {
                if (!isset($this->_regions[$region->getCountryId()])) $this->_regions[$region->getCountryId()] = array();
                $this->_regions[$region->getCountryId()][$region->getDefaultName()] = $region->getId();
            }
        }
        if (isset($this->_regions[$country][$regionName])) return $this->_regions[$country][$regionName];
        return 0;
    }
    /**
     * Prepare address
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param array $row
     * @param string $index
     * @param array $exclude
     * @return Mage_Customer_Model_Address
     */
    protected function prepareAddress($customer, $row, $index, $exclude = array())
    {
        $entityType = 'customer_address';
        $row = $this->extractRow($row, 'address_'.$index, $exclude);
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $row = $this->filterRow($row);
        try {
            $customerAddress = Mage::getModel('customer/address');
            $this->validateRow($entityType, $row, true);
            $row = $this->castRow($entityType, $row);
            if (!empty($row['firstname']) && count(explode(' ', $row['firstname']))) {
                $name = $this->parseAddressName($row['firstname']);
                if (count($name)) {
                    if (!empty($name['firstname'])) $row['firstname'] = $name['firstname'];
                    if (!empty($name['middlename'])) $row['middlename'] = $name['middlename'];
                    if (!empty($name['lastname'])) $row['lastname'] = $name['lastname'];
                }
            }
            if (!empty($row['name']) && empty($row['firstname']) && empty($row['lastname'])) {
                $name = $this->parseAddressName($row['name']);
                if (count($name)) {
                    if (!empty($name['firstname'])) $row['firstname'] = $name['firstname'];
                    if (!empty($name['middlename'])) $row['middlename'] = $name['middlename'];
                    if (!empty($name['lastname'])) $row['lastname'] = $name['lastname'];
                }
            }
            $this->prepareAddressAttributes($customerAddress, $row);
            $street = array();
            foreach (array('street1', 'street2', 'street3', ) as $field) {
                if (!empty($row[$field])) { $street[] = $row[$field]; }
            }
            if (count($street)) $customerAddress->setDataUsingMethod('street', $street);
            else if (!empty($row['street_full'])) $customerAddress->setStreet($row['street_full']);
            $customerAddress->setCountryId($row['country']);
            $regionName = isset($row['region']) ? $row['region'] : '';
            if ($regionName) {
                $regionId = $this->getRegionId($row['country'], $regionName);
                $customerAddress->setRegionId($regionId);
            }
            if (!empty($row['shipping']) && $row['shipping']) $customerAddress->setDefaultShipping(1);
            if (!empty($row['billing']) && $row['billing']) $customerAddress->setDefaultBilling(1);
            $customer->addAddress($customerAddress);
        } catch (Exception $e) {
            $this->addException($e->getMessage(), Mage_Dataflow_Model_Convert_Exception::WARNING);
        }
    }
    /**
     * Save row
     * 
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Customer_Convert_Adapter_Customer
     */
    public function saveRow($row)
    {
        $helper = $this->getHelper();
        $entityType = 'customer';
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $row = $this->filterRow($row);
        $customer = $this->getCustomerByRow($row);
        $this->validateRow($entityType, $row, (($customer->getId())? false : true));
        $row = $this->castRow($entityType, $row);
        $this->prepareGroup($customer, $row);
        $this->prepareStore($customer, $row);
        $this->preparePassword($customer, $row);
        $this->prepareAttributes($customer, $row);
        $this->prepareSubscription($customer, $row);
        $indexes = $this->getAddressesRowsIndexes($row);
        if (count($indexes)) $customer->getAddressesCollection()->delete();
        foreach ($indexes as $index) {
            $this->prepareAddress($customer, $row, $index, array());
        }
        $customer->setImportMode(true);
        $customer->save();
        $customerChanged = false;
        foreach ($customer->getAddressesCollection() as $customerAddress) {
            $customerAddress->setCustomerId($customer->getId());
            $customerAddress->save();
            if ($customerAddress->getDefaultShipping()) {
                $customer->setDefaultShipping($customerAddress->getId());
                $customerChanged = true;
            }
            if ($customerAddress->getDefaultBilling()) {
                $customer->setDefaultBilling($customerAddress->getId());
                $customerChanged = true;
            }
        }
        if ($customerChanged) $customer->save();
        return $this;
    }
}
