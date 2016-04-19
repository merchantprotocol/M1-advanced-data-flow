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
 * Source
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Entity_Attribute_Source extends Varien_Object
{
    /**
     * Attribute
     * 
     * @var Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    protected $_attribute;
    /**
     * Options array
     *
     * @var array
     */
    protected $_options;
    /**
     * Set attribute
     * 
     * @param Innoexts_AdvancedDataflow_Model_Entity_Attribute $attribute
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute_Source
     */
    public function setAttribute(Innoexts_AdvancedDataflow_Model_Entity_Attribute $attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }
    /**
     * Get attribute
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }
    /**
     * Get options
     * 
     * @return array
     */
    protected function _getOptions()
    {
        return array();
    }
    /**
     * Retrieve options 
     * 
     * @return array
     */
    public function getOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = $this->_getOptions();
        }
        return $this->_options;
    }
    /**
     * Get option value
     * 
     * @param array $options
     * @param string $value
     * @param string $valueKey
     * @return mixed
     */
    protected function _getOptionValue(& $options, $value, $valueKey = 'value')
    {
        $result = null;
        if (is_array($options)) {
            foreach ($options as $option) {
                if (strcasecmp($option['label'], $value) == 0) {
                    $result = (isset($option[$valueKey])) ? $option[$valueKey] : null;
                } else if (isset($option[$valueKey])) {
                    if (is_array($option[$valueKey])) {
                        $result = $this->_getOptionValue($option[$valueKey], $value, $valueKey);
                    } else if ($option[$valueKey] == $value) {
                        $result = $option[$valueKey];
                    }
                }
                if (!is_null($result)) break;
            }
        }
        return $result;
    }
    /**
     * Get option value
     * 
     * @param string $value
     * @param string $valueKey
     * @return mixed
     */
    public function getOptionValue($value, $valueKey = 'value')
    {
        $options = $this->getOptions();
        return $this->_getOptionValue($options, $value, $valueKey);
    }
    /**
     * Get option label
     * 
     * @param array $options
     * @param string $value
     * @param string $valueKey
     * @param string $labelKey
     * @return string
     */
    protected function _getOptionLabel(& $options, $value, $valueKey = 'value', $labelKey = 'label')
    {
        $result = null;
        if (is_array($options)) {
            foreach($options as $option) {
                if (isset($option[$valueKey])) {
                    if (is_array($option[$valueKey])) {
                        $result = $this->_getOptionLabel($option[$valueKey], $value, $valueKey, $labelKey);
                        if (!is_null($result)) break;
                    } else {
                        if ($option[$valueKey] == $value) {
                            $result = (isset($option[$labelKey])) ? $option[$labelKey] : $option[$valueKey];
                            break;
                        }
                    }
                }
            }
        }
        if (is_null($result) && isset($options[$value])) {
            $result = $options[$value];
        }
        return $result;
    }
    /**
     * Get option label
     * 
     * @param string $value
     * @param string $valueKey
     * @param string $labelKey
     * @return string
     */
    public function getOptionLabel($value, $valueKey = 'value', $labelKey = 'label')
    {
        $options = $this->getOptions();
        return $this->_getOptionLabel($options, $value, $valueKey, $labelKey);
    }
}