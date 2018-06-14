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
 * Entity abstract
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Entity_Abstract extends Varien_Object
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        if ($this->getType()) {
            $this->initialize();
        }
    }
    /**
     * Get helper
     * 
     * @return MP_AdvancedDataflow_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('advanceddataflow');
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
     * Set name
     * 
     * @param string $name
     * @return MP_AdvancedDataflow_Model_Entity_Abstract
     */
    public function setName($name)
    {
        $oldName = $this->getName();
        $this->setData('name', $name);
        if ($oldName != $name) {
            $this->initialize();
        }
        return $this;
    }
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Entity
     * 
     * @var MP_AdvancedDataflow_Model_Entity_Entity
     */
    protected $_entity;
    /**
     * XML node
     * 
     * @var Mage_Core_Model_Config_Element
     */
    protected $_node;
    /**
     * Translate elements
     * 
     * @var array
     */
    protected $_translateElements;
    /**
     * Source
     * 
     * @var MP_AdvancedDataflow_Model_Entity_Abstract
     */
    protected $_source;
    /**
     * Options
     * 
     * @var array
     */
    protected $_options;
    /**
     * Set entity
     * 
     * @param MP_AdvancedDataflow_Model_Entity_Entity $entity
     * @return MP_AdvancedDataflow_Model_Entity_Attribute
     */
    protected function setEntity(MP_AdvancedDataflow_Model_Entity_Entity $entity)
    {
        $this->_entity = $entity;
        return $this;
    }
    /**
     * Get entity
     * 
     * @return MP_AdvancedDataflow_Model_Entity_Entity
     */
    protected function getEntity()
    {
        return $this->_entity;
    }
    /**
     * Set node
     * 
     * @param Mage_Core_Model_Config_Element $node
     * @return MP_AdvancedDataflow_Model_Entity_Abstract
     */
    protected function setNode(Mage_Core_Model_Config_Element $node)
    {
        $this->_node = $node;
        return $this;
    }
    /**
     * Get node
     * 
     * @return Mage_Core_Model_Config_Element
     */
    protected function getNode()
    {
        return $this->_node;
    }
    /**
     * Get source name
     * 
     * @return string
     */
    public function getSourceName()
    {
        return $this->_getDataAsString('source');
    }
    /**
     * Get source
     * 
     * @return MP_AdvancedDataflow_Model_Entity_Attribute_Source
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            $sourceName = $this->getSourceName();
            if ($sourceName) {
                $source = Mage::getSingleton($sourceName);
                if ($source) {
                    $source->setAttribute($this);
                    $this->_source = $source;
                }
            }
        }
        return $this->_source;
    }
    /**
     * Check if source exists
     * 
     * @return boolean
     */
    public function hasSource()
    {
        $source = $this->getSource();
        if (!is_null($source)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get options
     * 
     * @return array
     */
    public function getOptions()
    {
        if (is_null($this->_options)) {
            $options = array();
            $optionsNodes = $this->getNodeElement('options');
            if ($optionsNodes) {
                foreach ($optionsNodes->children() as $optionNode) {
                    $label = $this->castAsString($optionNode->label);
                    $value = $this->castAsString($optionNode->value);
                    array_push($options, array('label' => $label, 'value' => $value, ));
                }
            }
            $this->_options = $options;
        }
        return $this->_options;
    }
    /**
     * Check if option value exists
     * 
     * @param string $value
     * @return boolean
     */
    protected function isOptionValueExists($value)
    {
        $source = $this->getSource();
        if ($source) {
            $optionValue = $source->getOptionValue($value);
            return (!is_null($optionValue)) ? true : false;
        } else {
            return false;
        }
    }
    /**
     * Clean value
     * 
     * @param unknown_type $value
     */
    public function clean($value)
    {
        $string = $this->getStringHelper();
        return $string->cleanString(trim($value));
    }
    /**
     * Cast as boolean
     * 
     * @param mixed $value
     * @return boolean
     */
    protected function castAsBoolean($value)
    {
        return ($value == '1' || strtolower($value) == 'true' || strtolower($value) == 'on') ? 1 : 0;
    }
    /**
     * Cast as string
     * 
     * @param mixed $value
     * @return string
     */
    protected function castAsString($value)
    {
        return (string) $value;
    }
    /**
     * Cast as datetime
     * 
     * @param mixed $value
     * @return string
     */
    protected function castAsDateTime($value)
    {
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        return gmstrftime($strftimeFormat, strtotime($value));
    }
    /**
     * Cast as date
     * 
     * @param mixed $value
     * @return string
     */
    protected function castAsDate($value)
    {
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATE_INTERNAL_FORMAT, true, true);
        return gmstrftime($strftimeFormat, strtotime($value));
    }
    /**
     * Cast as time
     * 
     * @param mixed $value
     * @return string
     */
    protected function castAsTime($value)
    {
        $strftimeFormat = Varien_Date::convertZendToStrftime('HH:mm:ss', true, true);
        return gmstrftime($strftimeFormat, strtotime($value));
    }
    /**
     * Cast as integer
     * 
     * @param mixed $value
     * @return integer
     */
    protected function castAsInteger($value)
    {
        return (int) $value;
    }
    /**
     * Cast as float
     * 
     * @param mixed $value
     * @return float
     */
    protected function castAsFloat($value)
    {
        return (float) $value;
    }
    /**
     * Cast as select
     * 
     * @param string $value
     * @return string
     */
    protected function castAsSelect($value)
    {
        $source = $this->getSource();
        if ($source) {
            return $source->getOptionValue($value);
        } else {
            return null;
        }
    }
    /**
     * Cast select as string
     * 
     * @param string $value
     * @return string
     */
    protected function castSelectAsString($value)
    {
        $source = $this->getSource();
        if ($source) {
            return $source->getOptionLabel($value, 'value', $this->getOptionAttribute());
        } else {
            return null;
        }
    }
    /**
     * Cast as multiple select
     * 
     * @param string $value
     * @return string
     */
    protected function castAsMultiSelect($value)
    {
        $source = $this->getSource();
        if ($source) {
            $result = null;
            $pieces = explode(',', $value);
            if (count($pieces)) {
                $result = array();
                foreach ($pieces as $piece) {
                    $_value = $source->getOptionValue(trim($piece));
                    if (!is_null($_value)) array_push($result, $_value); 
                }
                return $result;
                /* if (count($result)) $result = implode(',', $result);
                else $result = null; */
            }
            return $result;
        } else {
            return null;
        }
    }
    /**
     * Cast multiple select as string
     * 
     * @param string $value
     * @return string
     */
    protected function castMultiSelectAsString($value)
    {
        $source = $this->getSource();
        if ($source) {
            $result = array();
            if (!is_array($value)) $value = array($value);
            foreach ($value as $_value) {
                $__value = $source->getOptionLabel($_value);
                if (!is_null($_value)) array_push($result, $__value);
            }
            if (count($result)) return implode(',', $result);
            else return null;   
        } else {
            return null;
        }
    }
    /**
     * Cast as decimal
     * 
     * @param mixed $value
     * @param integer $scale
     * @return string
     */
    protected function castAsDecimal($value, $scale = 2)
    {
        return number_format($value, $scale, '.', '');
    }
    /**
     * Get node attribute
     * 
     * @param string $name
     * @return string
     */
    protected function getNodeAttribute($name)
    {
        $node = $this->getNode();
        if ($node) {
            return $node->getAttribute($name);
        } else {
            return null;
        }
    }
    /**
     * Get node attribute as boolean
     * 
     * @param string $name
     * @return boolean
     */
    protected function getNodeAttributeAsBoolean($name)
    {
        $nodeAttribute = $this->getNodeAttribute($name);
        if ($nodeAttribute) {
            return $this->castAsBoolean($nodeAttribute);
        } else {
            return null;
        }
    }
    /**
     * Get node attribute as string
     * 
     * @param string $name
     * @return string
     */
    protected function getNodeAttributeAsString($name)
    {
        $nodeAttribute = $this->getNodeAttribute($name);
        if ($nodeAttribute) {
            return $this->castAsString($nodeAttribute);
        } else {
            return null;
        }
    }
    /**
     * Get translate elements
     * 
     * @return array
     */
    protected function getTranslateElements()
    {
        if (is_null($this->_translateElements)) {
            $translateElements = array();
            $translate = $this->getNodeAttribute('translate');
            if ($translate) {
                $pieces = explode(' ', $translate);
                foreach ($pieces as $piece) {
                    if (!empty($piece)) {
                        array_push($translateElements, $piece);
                    }
                }
            }
            $this->_translateElements = $translateElements;
        }
        return $this->_translateElements;
    }
    /**
     * Check if element should be translated
     * 
     * @param string $name
     * @return boolean
     */
    protected function isTranslateElement($name)
    {
        $translateElements = $this->getTranslateElements();
        if (!is_null($translateElements) && in_array($name, $translateElements)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get node element
     * 
     * @param string $name
     * @return mixed
     */
    protected function getNodeElement($name)
    {
        $node = $this->getNode();
        if ($node && isset($node->$name)) {
            return $node->$name;
        } else {
            return null;
        }
    }
    /**
     * Get node element as boolean
     * 
     * @param string $name
     * @return boolean
     */
    protected function getNodeElementAsBoolean($name)
    {
        $node = $this->getNodeElement($name);
        if (!is_null($node)) {
            return $this->castAsBoolean($node);
        } else {
            return null;
        }
    }
    /**
     * Get node element as string
     * 
     * @param string $name
     * @return string
     */
    protected function getNodeElementAsString($name)
    {
        $node = $this->getNodeElement($name);
        if (!is_null($node)) {
            $value = $this->castAsString($node);
            if ($this->isTranslateElement($name)) {
                $value = $this->getHelper()->__($value);
            }
            return $value;
        } else {
            return null;
        }
    }
    /**
     * Get node element as integer
     * 
     * @param string $name
     * @return integer
     */
    protected function getNodeElementAsInteger($name)
    {
        $node = $this->getNodeElement($name);
        if (!is_null($node)) {
            return $this->castAsInteger($node);
        } else {
            return null;
        }
    }
    /**
     * Get data as boolean
     * 
     * @param string $key
     * @param boolean $default
     * @return boolean
     */
    protected function _getDataAsBoolean($key, $default = null)
    {
        if (!$this->hasData($key)) {
            $value = $this->getNodeElementAsBoolean($key);
            if (is_null($value) && !is_null($default)) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }
    /**
     * Get data as string
     * 
     * @param string $key
     * @param string $default
     * @return string
     */
    protected function _getDataAsString($key, $default = null)
    {
        if (!$this->hasData($key)) {
            $value = $this->getNodeElementAsString($key);
            if (is_null($value) && !is_null($default)) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }
    /**
     * Get data as integer
     * 
     * @param string $key
     * @param integer $default
     * @return integer
     */
    protected function _getDataAsInteger($key, $default = null)
    {
        if (!$this->hasData($key)) {
            $value = $this->getNodeElementAsInteger($key);
            if (is_null($value) && !is_null($default)) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }
    /**
     * Initialize node
     * 
     * @return MP_AdvancedDataflow_Model_Entity_Abstract
     */
    protected function initializeNode()
    {
        return $this;
    }
    /**
     * Initialize
     * 
     * @return MP_AdvancedDataflow_Model_Entity_Abstract
     */
    protected function initialize()
    {
        $this->initializeNode();
        return $this;
    }
    /**
     * Check if empty
     * 
     * @param mixed $value
     * @return boolean
     */
    protected function _isEmpty($value)
    {
        return (is_null($value) || ($value === '')) ? true : false;
    }
    /**
     * Get tab
     * 
     * @param int $count
     * @return string
     */
    protected function getTab($count = 1)
    {
        return str_pad("\t", $count);
    }
    /**
     * Get new line
     * 
     * @param int $count
     * @return string
     */
    protected function getNl($count = 1)
    {
        return str_pad("\r\n", $count);
    }
    /**
     * Get html line
     * 
     * @return string
     */
    protected function getHtmlLine($string, $tabs = 0)
    {
        return $this->getTab($tabs).$string;
    }
}
