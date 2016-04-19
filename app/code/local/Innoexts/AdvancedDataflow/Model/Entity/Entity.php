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
 * Entity
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Entity_Entity extends Innoexts_AdvancedDataflow_Model_Entity_Abstract
{
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Attributes
     * 
     * @var array
     */
    protected $_attributes;
    /**
     * Primary attribute
     * 
     * @var Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    protected $_primaryAttribute;
    /**
     * Children
     * 
     * @var array
     */
    protected $_children;
    /**
     * Child entities
     */
    protected $_childEntities = array();
    /**
     * Header tag
     * 
     * @var string
     */
    protected $_headerTag = 'h2';
    /**
     * Attributes tag
     * 
     * @var string
     */
    protected $_attributesTag = 'table';
    /**
     * Websites
     * 
     * @var array
     */
    protected $_websites;
    /**
     * Stores
     * 
     * @var array
     */
    protected $_stores;
    /**
     * Get config
     * 
     * @return Innoexts_AdvancedDataflow_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getSingleton('advanceddataflow/config');
    }
    /**
     * Initialize node
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function initializeNode()
    {
        parent::initializeNode();
        $node = $this->getConfig()->getEntity($this->getName());
        if (!$node) {
            Mage::throwException($this->getHelper()->__('Unknown entity "%s".', $this->getName()));
        } else {
            $this->setNode($node);
        }
        return $this;
    }
    /**
     * Initialize
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function initialize()
    {
        parent::initialize();
        return $this;
    }
    /**
     * Get identifier
     * 
     * @return string
     */
    public function getId()
    {
        return $this->getName().'_entity';
    }
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->_getDataAsString('title');
    }
    /**
     * Get Eav flag
     * 
     * @return boolean
     */
    public function getEav()
    {
        if (!$this->hasData('eav')) {
            $this->setData('eav', $this->getNodeAttributeAsBoolean('eav'));
        }
        return $this->getData('eav');
    }
    /**
     * Check if entity is eav
     * 
     * @return boolean
     */
    public function isEav()
    {
        return $this->getEav();
    }
    /**
     * Get model name
     * 
     * @return string
     */
    public function getModelName()
    {
        return $this->_getDataAsString('model');
    }
    /**
     * Get model
     * 
     * @return Mage_Core_Model_Abstract
     */
    protected function _getModel()
    {
        return Mage::getModel($this->getModelName());
    }
    /**
     * Get singleton
     * 
     * @return Mage_Core_Model_Abstract
     */
    public function getModelSingleton()
    {
        return Mage::getSingleton($this->getModelName());
    }
    /**
     * Get table name
     * 
     * @return string
     */
    public function getTableName()
    {
        return $this->_getDataAsString('table');
    }
    /**
     * Get table
     * 
     * @return string
     */
    public function getTable()
    {
        $resource = Mage::getSingleton('core/resource');
        return $resource->getTableName($this->getTableName());
    }
    /**
     * Get getter
     * 
     * @return string
     */
    public function getGetter()
    {
        return $this->getData('getter');
    }
    /**
     * Get relation type
     * 
     * @return string
     */
    public function getRelation()
    {
        return $this->getData('relation');
    }
    /**
     * Get index
     * 
     * @return string
     */
    public function getIndex()
    {
        return $this->getData('index');
    }
    /**
     * Get prefix
     * 
     * @return string
     */
    public function getPrefix($underscored = false)
    {
        $prefix = $this->getData('prefix');
        if ($prefix) {
            $relation = $this->getRelation();
            if ($relation == 'many') {
                return $prefix.'_'.$this->getIndex().(($underscored) ? '_' : '');
            } else {
                return $prefix.(($underscored) ? '_' : '');
            }
        } else {
            return null;
        }
    }
    /**
     * Get child entity
     * 
     * @param string $model
     * @param string $name
     * @param string $alias
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function getChildEntity($model, $name, $alias)
    {
        if (!isset($this->_childEntities[$alias])) {
            $this->_childEntities[$alias] = Mage::getModel($model, array('name' => $name));
        }
        if (isset($this->_childEntities[$alias])) {
            return $this->_childEntities[$alias];
        } else {
            return null;
        }
    }
    /**
     * Get children
     * 
     * @return array
     */
    protected function getChildren()
    {
        if (is_null($this->_children)) {
            $children = array();
            $childrenNode = $this->getNodeElement('children');
            if ($childrenNode) {
                foreach ($childrenNode->children() as $alias => $node) {
                    $type = $this->castAsString($node->type);
                    $model = $this->castAsString($node->model);
                    if ($model && $type) {
                        $entity = $this->getChildEntity($model, str_replace('/', '_', $type), $alias);
                        if ($entity) {
                            $prefix = $this->castAsString($node->prefix);
                            $relation = $this->castAsString($node->relation);
                            $getter = $this->castAsString($node->getter);
                            $entity->setPrefix(((!empty($prefix)) ? $prefix : 'entity'));
                            $entity->setRelation(((!empty($relation)) ? $relation : 'many'));
                            $entity->setGetter(((!empty($getter)) ? $getter : 'getItems'));
                            $entity->setEntity($this);
                            $children[$alias] = $entity;
                        }
                    }
                }
            }
            $this->_children = $children;
        }
        return $this->_children;
    }
    /**
     * Get attribute model name
     * 
     * @return string
     */
    protected function getAttributeModelName()
    {
        return 'advanceddataflow/entity_attribute';
    }
    /**
     * Get attribute model
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    protected function getAttributeModel()
    {
        return Mage::getModel($this->getAttributeModelName());
    }
    /**
     * Get attribute singleton
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    protected function getAttributeSingleton()
    {
        return Mage::getSingleton($this->getAttributeModelName());
    }
    /**
     * Sort attributes
     *
     * @param Innoexts_AdvancedDataflow_Model_Entity_Attribute $attribute1
     * @param Innoexts_AdvancedDataflow_Model_Entity_Attribute $attribute2
     * @return int
     */
    protected function _sortAttributes($attribute1, $attribute2)
    {
        if ($attribute1->getSortOrder() != $attribute2->getSortOrder()) {
            return $attribute1->getSortOrder() < $attribute2->getSortOrder() ? -1 : 1;
        } else {
            return 0;
        }
    }
    /**
     * Sort attributes
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function sortAttributes()
    {
        if (!is_null($this->_attributes)) {
            $attributes = $this->_attributes;
            usort($attributes, array($this, '_sortAttributes'));
            $_attributes = array();
            foreach ($attributes as $attribute) {
                $_attributes[$attribute->getName()] = $attribute;
            }
            $this->_attributes = $_attributes;
            unset($attributes);
            unset($_attributes);
        }
        return $this;
    }
    /**
     * Get attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $attributes = array();
            $attributesNode = $this->getNodeElement('attributes');
            if ($attributesNode) {
                foreach ($attributesNode->children() as $name => $node) {
                    
                    $type = $node;
                    
                    $attribute = $this->getAttributeModel();
                    
                    $attribute->setNode($node);
                    $attribute->setEntity($this);
                    $attribute->setName($name);
                    $attributes[$name] = $attribute;
                }
            }
            $this->_attributes = $attributes;
            $this->sortAttributes();
        }
        return $this->_attributes;
    }
    /**
     * Get attributes names
     * 
     * @return array
     */
    protected function getAttributesNames()
    {
        return array_keys($this->getAttributes());
    }
    /**
     * Check if attribute exists
     * 
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name)
    {
        $attribute = $this->getAttribute($name);
        if (!is_null($attribute)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get attribute
     * 
     * @param string $name
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    public function getAttribute($name)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        } else {
            return null;
        }
    }
    /**
     * Get primary attribute
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    public function getPrimaryAttribute()
    {
        if (is_null($this->_primaryAttribute)) {
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute->isPrimary()) {
                    $this->_primaryAttribute = $attribute;
                    break;
                }
            }
        }
        return $this->_primaryAttribute;
    }
    /**
     * Get collection model name
     * 
     * @return string
     */
    public function getCollectionModelName()
    {
        return $this->_getDataAsString('collection_model');
    }
    /**
     * Get collection
     * 
     * @return Varien_Data_Collection_Db
     */
    public function getCollection()
    {
        $collectionModelName = $this->getCollectionModelName();
        return Mage::getResourceModel($collectionModelName);
    }
    /**
     * Get ids by collection
     * 
     * @param Varien_Data_Collection_Db $collection
     * @return array
     */
    protected function getIdsByCollection(Varien_Data_Collection_Db $collection)
    {
        return $collection->getAllIds();
    }
    /**
     * Get objects identifiers by filters
     * 
     * @param $filters
     * @return array
     */
    public function getIdsByFilters($filters = array())
    {
        $ids = array();
        $collection = $this->getCollection();
        if ($collection) {
            $collection->distinct(true);
            $ids = $this->getIdsByCollection($collection);
        }
        return $ids;
    }
    /**
     * Get cache name
     * 
     * @return string
     */
    protected function getModelCacheName()
    {
        return 'model_cache_'.$this->getName();
    }
    /**
     * Set model
     * 
     * @param Mage_Core_Model_Abstract $model
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function setModel(Mage_Core_Model_Abstract $model)
    {
        $id = Mage::objects()->save($model);
        Mage::unregister($this->getModelCacheName());
        Mage::register($this->getModelCacheName(), $id);
        return $this;
    }
    /**
     * Get model
     * 
     * @return Mage_Core_Model_Abstract
     */
    protected function __getModel()
    {
        return Mage::objects()->load(Mage::registry($this->getModelCacheName()));
    }
    /**
     * Get model
     * 
     * @return Mage_Core_Model_Abstract
     */
    public function getModel()
    {
        $model = $this->__getModel();
        if (!$model) {
            $this->setModel($this->_getModel());
            $model = $this->__getModel();
        }
        return $model;
    }
    /**
     * Reset model
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function resetModel()
    {
        $model = $this->getModel();
        if ($model) {
            if (method_exists($model, 'reset')) {
                $model->reset();
            } else {
                foreach ($model->getData() as $value) {
                    if (is_object($value) && method_exists($value, 'reset')) {
                        $value->reset();
                    }
                }
                $model->setData(array());
                $model->setOrigData();
            }
        }
        return $this;
    }
    /**
     * Reload model
     * 
     * @param mixed $id
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function reloadModel($id)
    {
        $this->resetModel();
        $model = $this->getModel();
        if ($model) {
            $model->load($id);
        }
        return $this;
    }
    /**
     * Load model by attribute
     * 
     * @param string $name
     * @param string $value
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function loadModelByAttribute($name, $value)
    {
        $model = $this->getModel();
        if ($model) {
            if ($this->isEav()) {
                $model = $model->loadByAttribute($name, $value);
                if ($model) {
                    $this->setModel($model);
                }
            } else {
                $model->load($value, $name);
            }
        }
        return $this;
    }
    /**
     * Load model by data
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function loadModelByData($data)
    {
        $model = $this->getModel();
        if ($model) {
            $primaryAttribute = $this->getPrimaryAttribute();
            if ($primaryAttribute) {
                $name = $primaryAttribute->getName();
                $value = (isset($data[$name])) ? $primaryAttribute->clean($data[$name]) : null;
                if ($value) {
                    $this->loadModelByAttribute($name, $value);
                }
            }
        }
        return $this;
    }
    /**
     * Reload model by data
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function reloadModelByData($data)
    {
        $this->resetModel();
        $this->loadModelByData($data);
        return $this;
    }
    /**
     * Save model
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function saveModel()
    {
        $model = $this->getModel();
        if ($model) {
            $model->save();
        }
        return $this;
    }
    /**
     * Check if model is new
     * 
     * @return boolean
     */
    protected function isModelNew()
    {
        $model = $this->getModel();
        return ($model && $model->getId()) ? false : true;
    }
    /**
     * Get model identifier
     * 
     * @return mixed
     */
    public function getModelId()
    {
        return $this->getModel()->getId();
    }
    /**
     * Convert child entity to array
     * 
     * @param string $name
     * @param Innoexts_AdvancedDataflow_Model_Entity_Entity $child
     * @return array
     */
    protected function convertChildToArray($name, Innoexts_AdvancedDataflow_Model_Entity_Entity $child)
    {
        $data = array();
        $model = $this->getModel();
        if ($model) {
            $getter = $child->getGetter();
            $relation = $child->getRelation();
            if ($relation == 'many') {
                $childModels = $model->$getter();
                if (!empty($childModels) && count($childModels)) {
                    foreach ($childModels as $index => $childModel) {
                        $child->setIndex($index);
                        $child->setModel($childModel);
                        $data = array_merge($data, $child->convertEntityToArray());
                    }
                }
            } else {
                $childModel = $model->$getter();
                if ($childModel) {
                    $child->setModel($childModel);
                    $data = array_merge($data, $child->convertEntityToArray());
                }
            }
        }
        return $data;
    }
    /**
     * Convert children entities to array
     * 
     * @return array
     */
    protected function convertChildrenToArray()
    {
        $data = array();
        foreach ($this->getChildren() as $name => $child) {
            $data = array_merge($data, $this->convertChildToArray($name, $child));
        }
        return $data;
    }
    /**
     * Convert entity to array
     * 
     * @return array
     */
    protected function _convertEntityToArray($action = 'export')
    {
        $array = array();
        $model = $this->getModel();
        if ($model) {
            $prefix = $this->getPrefix(true);
            $data = $model->getData();
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute->isEnabled() && $attribute->isActionEnabled($action)) {
                    $attributeName = $attribute->getName();
                    if (isset($data[$attributeName])) {
                        $value = $data[$attributeName];
                        $array[((!is_null($prefix)) ? $prefix : '').$attributeName] = $attribute->getText($value);
                    }
                }
            }
        }
        return $array;
    }
    /**
     * Convert entity to array
     * 
     * @return array
     */
    public function convertEntityToArray($action = 'export')
    {
        $data = $this->_convertEntityToArray($action);
        $childrenData = $this->convertChildrenToArray();
        if (count($childrenData)) {
            $data = array_merge($data, $childrenData);
        }
        return $data;
    }
    /**
     * Filter data
     * 
     * @param array $data
     * @param array $actions
     * @return array
     */
    protected function filterData($data, $actions)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $attribute = $this->getAttribute($key);
            if ($attribute) {
                foreach ($actions as $action) {
                    if ($attribute->isActionEnabled($action)) { 
                        $result[$key] = $value;
                        break;
                    }
                }
            }
        }
        return $result;
    }
    /**
     * Clean data
     * 
     * @param array $data
     * @return array
     */
    protected function cleanData($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = $this->clean($value);
        }
        return $result;
    }
    /**
     * Validate required data
     * 
     * @param array $data
     * @param string $action
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function validateRequiredData($data, $action)
    {
        foreach ($this->getAttributes() as $attribute) {
            $name = $attribute->getName();
            if ($attribute->isRequired($action) && ((!isset($data[$name])) || ($this->_isEmpty($data[$name])))) {
                $message = $this->getHelper()->__('Required field "%s" is not defined.', $name);
                Mage::throwException($message);
            }
        }
        return $this;
    }
    /**
     * Validate data
     * 
     * @param array $data
     * @param array $action
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function validateData($data, $action)
    {
        $this->validateRequiredData($data, $action);
        foreach ($data as $key => $value) {
            $attribute = $this->getAttribute($key);
            if ($attribute) {
                if (!$attribute->validate($value)) {
                    $message = $this->getHelper()->__('Field "%s" is not valid.', $key);
                    Mage::throwException($message);
                }
            }
        }
        return $this;
    }
    /**
     * Cast data
     * 
     * @param array $data
     * @return array
     */
    protected function castData($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $attribute = $this->getAttribute($key);
            if ($attribute) {
                $result[$key] = $attribute->cast($value);
            }
        }
        return $result;
    }
    /**
     * Set model data for attributes
     * 
     * @param array $data
     * @param array $attributes
     * @param boolean $usingMethod
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function _setModelData($data, $attributes = null, $usingMethod = false)
    {
        $model = $this->getModel();
        if ($model) {
            if (is_null($attributes)) $attributes = $this->getAttributesNames();
            foreach ($attributes as $key) {
                if (isset($data[$key])) {
                    $attribute = $this->getAttribute($key);
                    if (!$attribute) continue;
                    if (!$usingMethod && !$attribute->isMethodSetter()) $model->setData($key, $data[$key]);
                    else $model->setDataUsingMethod($key, $data[$key]);
                }
            }
        }
        return $this;
    }
    /**
     * Get data indexes
     * 
     * @param string $prefix
     * @param array $data
     * @return array
     */
    protected function getDataIndexes($prefix, $data)
    {
        $string = $this->getStringHelper();
        $indexes = array();
        foreach ($data as $key => $value) {
            if ($string->substr($key, 0, strlen($prefix)) == $prefix) {
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
     * Extract data
     * 
     * @param array $data
     * @return array
     */
    protected function extractData($data)
    {
        $result = array();
        if (count($data)) {
            $prefix = $this->getPrefix(true);
            foreach ($data as $key => $value) {
                if (!empty($prefix)) {
                    $length = strlen($prefix);
                    if (substr($key, 0, $length) == $prefix) {
                        $attributeName = substr($key, strlen($prefix));
                    } else {
                        $attributeName = null;
                    }
                } else {
                    $attributeName = $key;
                }
                if ($attributeName) {
                    $attribute = $this->getAttribute($attributeName);
                    if (!$attribute) {
                        continue;
                    }
                    $result[$attributeName] = $data[$key];
                }
            }
        }
        return $result;
    }
    /**
     * After child data save
     * 
     * @param Innoexts_AdvancedDataflow_Model_Entity_Entity $child
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function afterChildDataSet(Innoexts_AdvancedDataflow_Model_Entity_Entity $child)
    {
        # _deb($child->getModel()->toArray());
        return $this;
    }
    /**
     * Set child data
     * 
     * @param array $data
     * @param string $name
     * @param Innoexts_AdvancedDataflow_Model_Entity_Entity $child
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function setChildData($data, $name, Innoexts_AdvancedDataflow_Model_Entity_Entity $child)
    {
        $indexes = $this->getDataIndexes($name, $data);
        if (count($indexes)) {
            foreach ($indexes as $index) {
                $child->setIndex($index);
                $childData = $child->extractData($data);
                $child->setModelData($childData, 'add');
                $this->afterChildDataSet($child);
            }
        }
        return $this;
    }
    /**
     * Set children data
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function setChildrenData($data)
    {
        foreach ($this->getChildren() as $name => $child) {
            $this->setChildData($data, $name, $child);
        }
        return $this;
    }
    /**
     * Set model data
     * 
     * @param array $data
     * @param string $action
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function setModelData($data, $action)
    {
        $roughData = $data;
        $data = $this->filterData($data, array($action));
        $this->validateData($data, $action);
        $this->_setModelData($this->castData($data), null, false);
        $this->setChildrenData($roughData);
        return $this;
    }
    /**
     * Add model
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function addModel($data)
    {
        $this->setModelData($data, 'add');
        $this->saveModel();
    }
    /**
     * Edit model
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function editModel($data)
    {
        $this->setModelData($data, 'edit');
        $this->saveModel();
    }
    /**
     * Save model
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    public function saveModelData($data)
    {
        $data = $this->cleanData($data);
        $this->reloadModelByData($data);
        if ($this->isModelNew()) { $this->addModel($data);} else { $this->editModel($data); }
        return $this;
    }
    /**
     * Retrieve website
     * 
     * @param string $code
     * @return Mage_Core_Model_Website
     */
    protected function getWebsite($code)
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites(true, true);
        }
        if (isset($this->_websites[$code])) {
            return $this->_websites[$code];
        }
        return false;
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
     * Get default store code
     * 
     * @return string
     */
    protected function getDefaultStoreCode()
    {
        $store = $this->getVar('defaultStore', 'default');
        $stores = array_keys($this->getStores());
        if (in_array($store, $stores)) {
            return $store;
        } else {
            return array_shift($stores);
        }
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
        if (isset($this->_stores[$code])) {
            return $this->_stores[$code];
        } else {
            return null;
        }
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
            if ($id == $_store->getId()) { 
                $store = $_store; 
                break; 
            } 
        }
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
     * Get attributes tag
     * 
     * @return string
     */
    protected function getAttributesTag()
    {
        return $this->_attributesTag;
    }
    /**
     * Get header tag
     * 
     * @return string
     */
    protected function getHeaderTag()
    {
        return $this->_headerTag;
    }
    /**
     * Get attributes html
     * 
     * @param int $tabs
     * @param bool $withOptions
     * @return string
     */
    public function getAttributesHtml($tabs = 0, $withOptions = false)
    {
        $html = array();
        $html[] = $this->getHtmlLine('<'.$this->getAttributesTag().'>', $tabs);
        $html[] = $this->getAttributeSingleton()->getHeaderHtml($tabs + 1);
        foreach ($this->getAttributes() as $attribute) {
            if (!$attribute->isEnabled()) continue;
            $html[] = $attribute->getHtml($tabs + 1, $withOptions);
        }
        $html[] = $this->getHtmlLine('</'.$this->getAttributesTag().'>', $tabs);
        return implode($this->getNl(), $html);
    }
    /**
     * Get html
     * 
     * @param int $tabs
     * @return string
     */
    public function getHtml($tabs = 0)
    {
        $html = array();
        $html[] = $this->getHtmlLine('<'.$this->getHeaderTag().' id="'.$this->getId().'">'.$this->getTitle().
            '</'.$this->getHeaderTag().'>', $tabs);
        $html[] = $this->getAttributesHtml($tabs, false);
        return implode($this->getNl(), $html);
    }
}