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
 * Product adapter
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product extends MP_AdvancedDataflow_Model_Eav_Convert_Adapter_Entity
{
    /**
     * Product types
     *
     * @var array
     */
    protected $_productTypes;
    /**
     * Product attribute set
     *
     * @var array
     */
    protected $_productAttributeSets;
    /**
     * Product type instances
     *
     * @var array
     */
    protected $_productTypeInstances = array();
    /**
     * Inventory fields array
     * 
     * @var array
     */
    protected $_inventoryFields             = array();
    /**
     * Inventory fields by product types
     *
     * @var array
     */
    protected $_inventoryFieldsProductTypes = array();
    /**
     * Numeric fields
     * 
     * @var array
     */
    protected $_toNumber = array();
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setVar('entity_type', 'catalog/product');
        if (!$this->hasProduct()) $this->setProduct(Mage::getModel('catalog/product'));
    }
    /**
     * Check if product exists
     * 
     * @return boolean
     */
    public function hasProduct()
    {
        return (Mage::registry('Object_Cache_Product')) ? true : false;
    }
    /**
     * Set product
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $id = Mage::objects()->save($product);
        Mage::register('Object_Cache_Product', $id);
        return $this;
    }
    /**
     * Get product
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_Product'));
    }
    /**
     * Initialize fields
     * 
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function initializeFields()
    {
        parent::initializeFields();
        $this->initializeEntityTypeFields('catalog_product');
        return $this;
    }
    /**
     * Initialize entity type field
     * 
     * @param string $entityType
     * @param string $fieldName
     * @param Mage_Core_Model_Config_Element $fieldNode
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function initializeEntityTypeField($entityType, $fieldName, $fieldNode)
    {
        parent::initializeEntityTypeField($entityType, $fieldName, $fieldNode);
        if ($fieldNode->is('inventory')) {
            foreach ($fieldNode->product_type->children() as $productType) {
                $productType = $productType->getName();
                $this->_inventoryFieldsProductTypes[$productType][] = $fieldName;
                if ($fieldNode->is('use_config')) $this->_inventoryFieldsProductTypes[$productType][] = 'use_config_'.$fieldName;
            }
            $this->_inventoryFields[] = $fieldName;
            if ($fieldNode->is('use_config')) $this->_inventoryFields[] = 'use_config_'.$fieldName;
        }
        if ($fieldNode->is('to_number')) $this->_toNumber[] = $fieldName;
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
        if ($entityType == 'catalog_product') return Mage::getResourceModel('catalog/product_attribute_collection');
        else return array();
    }
    /**
     * Get inventory fields
     * 
     * @param string $productType
     * @return array
     */
    protected function getInventoryFields($productType = null)
    {
        if ($productType) {
            if (isset($this->_inventoryFieldsProductTypes[$productType])) 
                return $this->_inventoryFieldsProductTypes[$productType];
            else return array();
        } else return $this->_inventoryFields;
    }
    /**
     * Retrieve product types
     *
     * @return array
     */
    public function getProductTypes()
    {
        if (is_null($this->_productTypes)) {
            $this->_productTypes = array();
            $options = Mage::getModel('catalog/product_type')->getOptionArray();
            foreach ($options as $key => $value) { $this->_productTypes[$key] = $key; }
        }
        return $this->_productTypes;
    }
    /**
     * Retrieve product attribute set collection array
     *
     * @return array
     */
    public function getProductAttributeSets()
    {
        if (is_null($this->_productAttributeSets)) {
            $this->_productAttributeSets = array();
            $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
            $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeId);
            foreach ($collection as $set) { $this->_productAttributeSets[$set->getAttributeSetName()] = $set->getId(); }
        }
        return $this->_productAttributeSets;
    }
    /**
     * ReDefine Product Type Instance to Product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Convert_Adapter_Product
     */
    public function setProductTypeInstance(Mage_Catalog_Model_Product $product)
    {
        $type = $product->getTypeId();
        if (!isset($this->_productTypeInstances[$type])) {
            $this->_productTypeInstances[$type] = Mage::getSingleton('catalog/product_type')->factory($product, true);
        }
        $product->setTypeInstance($this->_productTypeInstances[$type], true);
        return $this;
    }
    /**
     * Get and load product by row
     * 
     * @param array $row
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductByRow($row)
    {
        $helper = $this->getHelper();
        $product = $this->getProduct();
        $product->reset();
        if (empty($row['store'])) {
            $message = $helper->__('Skipping import row, required field "%s" is not defined.', 'store');
            Mage::throwException($message);
        }
        $store = $this->getStoreByCode($row['store']);
        if ($store === false) {
            $message = $helper->__('Skipping import row, store "%s" field does not exist.', $row['store']);
            Mage::throwException($message);
        }
        if (empty($row['sku'])) {
            $message = $helper->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($row['sku']);
        if ($productId) $product->load($productId);
        return $product;
    }
    /**
     * Prepare type
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareType($product, $row)
    {
        $helper = $this->getHelper();
        if (!$product->getId()) {
            $productTypes = $this->getProductTypes();
            $productTypeId = (!empty($row['type']) && isset($productTypes[strtolower($row['type'])])) ? 
                $productTypes[strtolower($row['type'])] : null;
            if (!$productTypeId) {
                $value = isset($row['type']) ? $row['type'] : '';
                $message = $helper->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypeId);
        }
        return $this;
    }
    /**
     * Prepare attribute set
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareAttributeSet($product, $row)
    {
        $helper = $this->getHelper();
        if (!$product->getId()) {
            $productAttributeSets = $this->getProductAttributeSets();
            $productAttributeSetId = (!empty($row['attribute_set']) && isset($productAttributeSets[$row['attribute_set']])) ? 
                $productAttributeSets[$row['attribute_set']] : null;
            if (!$productAttributeSetId) {
                $value = isset($row['attribute_set']) ? $row['attribute_set'] : '';
                $message = $helper->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSetId);
        }
        return $this;
    }
    /**
     * Prepare category
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareCategory($product, $row)
    {
        if (!empty($row['category_ids'])) $product->setCategoryIds($row['category_ids']);
        return $this;
    }
    /**
     * Prepare website
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareWebsite($product, $row)
    {
        $store = $this->getStoreByCode($row['store']);
        if ($store && ($store->getId() != 0)) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) $websiteIds = array();
            if (!in_array($store->getWebsiteId(), $websiteIds)) $websiteIds[] = $store->getWebsiteId();
            $product->setWebsiteIds($websiteIds);
        }
        if (isset($row['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) $websiteIds = array();
            $websiteCodes = explode(',', $row['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) { $websiteIds[] = $website->getId(); }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }
        return $this;
    }
    /**
     * Prepare attributes
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareAttributes($product, $row)
    {
        $entityType = 'catalog_product';
        foreach ($row as $field => $value) {
            $attribute = $this->getAttribute($entityType, $field);
            if (!$attribute) continue;
            if ($attribute->getFrontendInput() == 'multiselect') $value = explode(' , ', $value);
            $product->setData($field, $value);
        }
        return $this;
    }
    /**
     * Prepare visibility
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareVisibility($product, $row)
    {
        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }
        return $this;
    }
    /**
     * Prepare stock
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareStock($product, $row)
    {
        $stockData = array();
        $inventoryFields = $this->getInventoryFields($product->getTypeId());
        foreach ($inventoryFields as $field) {
            if (isset($row[$field])) {
                if (in_array($field, $this->_toNumber)) $stockData[$field] = $this->getNumber($row[$field]);
                else $stockData[$field] = $row[$field];
            }
        }
        $product->setStockData($stockData);
        return $this;
    }
    /**
     * Prepare media
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    protected function prepareMedia($product, $row)
    {
        $entityType = 'catalog_product';
        $productMediaGalleryAttribute = $this->getAttribute($entityType, 'media_gallery');
        if (!$productMediaGalleryAttribute) return $this;
        $mediaGalleryBackendModel = $productMediaGalleryAttribute->getBackend();
        $arrayToMassAdd = array();
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($row[$mediaAttributeCode])) {
                $file = trim($row[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }
        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product, $arrayToMassAdd, Mage::getBaseDir('media').DS.'import', false, false
        );
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($row[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($row[$mediaAttributeCode . '_label']);
                if (isset($row[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($row[$mediaAttributeCode], $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) 
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                }
                if (!$addedFile) $addedFile = $product->getData($mediaAttributeCode);
                if ($fileLabel && $addedFile) 
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
            }
        }
        return $this;
    }
    /**
     * Save row
     * 
     * @param array $row
     * @return MP_AdvancedDataflow_Model_Catalog_Convert_Adapter_Product
     */
    public function saveRow($row)
    {
        $helper = $this->getHelper();
        $entityType = 'catalog_product';
        $row = $this->unsetRowIgnoreFields($entityType, $row);
        $row = $this->filterRow($row);
        $product = $this->getProductByRow($row);
        $this->validateRow($entityType, $row, (($product->getId())? false : true));
        $row = $this->castRow($entityType, $row);
        $this->prepareType($product, $row)
            ->prepareAttributeSet($product, $row)
            ->setProductTypeInstance($product)
            ->prepareCategory($product, $row)
            ->prepareWebsite($product, $row)
            ->prepareAttributes($product, $row)
            ->prepareVisibility($product, $row)
            ->prepareStock($product, $row)
            ->prepareMedia($product, $row);
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        $product->save();
        return $this;
    }
}
