<?php
/**
 * Merchant Protocol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Merchant Protocol Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://merchantprotocol.com/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@merchantprotocol.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.merchantprotocol.com for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2016 Merchant Protocol LLC. and affiliates (https://merchantprotocol.com/)
 * @license    https://merchantprotocol.com/commercial-license/  Merchant Protocol Commercial License (MPCL 1.0)
 */

/**
 * Entity attribute
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Entity_Attribute extends Innoexts_AdvancedDataflow_Model_Entity_Abstract
{
    
    
    
    
    
    
    
    
    
    
    /**
     * Actions
     * 
     * @var array
     */
    protected $_actions;
    /**
     * Master attributes
     * 
     * @var array
     */
    protected $_masterAttributes;
    /**
     * Required flags
     * 
     * @var array
     */
    protected $_required;
    /**
     * Validate rules
     * 
     * @var array
     */
    protected $_validateRules;
    /**
     * Options tag
     * 
     * @var string
     */
    protected $_optionsTag = 'table';
    /**
     * Option tag
     * 
     * @var string
     */
    protected $_optionTag = 'tr';
    /**
     * Option attribute header tag
     * 
     * @var string
     */
    protected $_optionAttributeHeaderTag = 'th';
    /**
     * Option attribute tag
     * 
     * @var string
     */
    protected $_optionAttributeTag = 'td';
    /**
     * Tag
     * 
     * @var string
     */
    protected $_tag = 'tr';
    /**
     * Column header tag
     * 
     * @var string
     */
    protected $_columnHeaderTag = 'th';
    /**
     * Column tag
     * 
     * @var string
     */
    protected $_columnTag = 'td';
    /**
     * Get identifier
     * 
     * @return string
     */
    public function getId()
    {
        $entity = $this->getEntity();
        $id = $this->getName().'_attribute';
        if ($entity) {
            return $entity->getId().'_'.$id;
        } else {
            return $id;
        }
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
     * Get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->_getDataAsString('description');
    }
    /**
     * Get allowed types
     * 
     * @return array
     */
    protected function getAllowedTypes()
    {
        return array(
            'boolean', 'string', 'datetime', 'date', 'time', 'integer', 'float', 'decimal', 'amount', 'select', 'multiselect', 
        );
    }
    /**
     * Get select types
     * 
     * @return array
     */
    protected function getSelectTypes()
    {
        return array('select', 'multiselect', );
    }
    /**
     * Get allowed option attributes
     * 
     * @return array
     */
    protected function getAllowedOptionAttributes()
    {
        return array('value', 'label', );
    }
    /**
     * Get option attribute
     * 
     * @return string
     */
    public function getOptionAttribute()
    {
        if (!$this->hasData('option_attribute')) {
            $attributes = $this->getAllowedOptionAttributes();
            $attribute = $this->getNodeElementAsString('option_attribute');
            if (!empty($attribute) && in_array($attribute, $attributes)) {
                $this->setData('option_attribute', $attribute);
            } else {
                $this->setData('option_attribute', 'value');
            }
        }
        return $this->getData('option_attribute');
    }
    /**
     * Get type
     * 
     * @return string
     */
    public function getType()
    {
        if (!$this->hasData('type')) {
            $types = $this->getAllowedTypes();
            $type = $this->getNodeElementAsString('type');
            if (!empty($type) && in_array($type, $types)) $this->setData('type', $type);
            else $this->setData('type', 'string');
        }
        return $this->getData('type');
    }
    /**
     * Check if attribute has select type
     * 
     * @return bool
     */
    public function isSelectType()
    {
        return in_array($this->getType(), $this->getSelectTypes());
    }
    /**
     * Get scale
     * 
     * @return integer
     */
    public function getScale()
    {
        return $this->_getDataAsInteger('scale');
    }
    /**
     * Get unique value
     * 
     * @return boolean
     */
    public function getUnique()
    {
        return $this->_getDataAsBoolean('unique');
    }
    /**
     * Check if attribute is unique 
     * 
     * @return boolean
     */
    public function isUnique()
    {
        return $this->getUnique();
    }
    /**
     * Get primary value
     * 
     * @return boolean
     */
    public function getPrimary()
    {
        return $this->_getDataAsBoolean('primary');
    }
    /**
     * Check if attribute is primary 
     * 
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->getPrimary();
    }
    /**
     * Get static value
     * 
     * @return boolean
     */
    public function getStatic()
    {
        return $this->_getDataAsBoolean('static');
    }
    /**
     * Check if attribute is static 
     * 
     * @return boolean
     */
    public function isStatic()
    {
        return $this->getStatic();
    }
    /**
     * Get enabled value
     * 
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->_getDataAsBoolean('enabled', true);
    }
    /**
     * Check if attribute is enabled 
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }
    /**
     * Get show options in description
     * 
     * @return boolean
     */
    public function getShowOptionsInDescription()
    {
        return $this->_getDataAsBoolean('show_options_in_description', true);
    }
    /**
     * Check if show options in description flag enabled
     * 
     * @return boolean
     */
    public function isShowOptionsInDescription()
    {
        return $this->getShowOptionsInDescription();
    }
    /**
     * Get method setter
     * 
     * @return boolean
     */
    public function getMethodSetter()
    {
        return $this->_getDataAsBoolean('method_setter');
    }
    /**
     * Check if method setter
     * 
     * @return boolean
     */
    public function isMethodSetter()
    {
        return $this->getMethodSetter();
    }
    /**
     * Get sort order
     * 
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->_getDataAsInteger('sort_order');
    }
    /**
     * Get actions
     * 
     * @return array
     */
    public function getActions()
    {
        if (is_null($this->_actions)) {
            $actions = array();
            $actionsNodes = $this->getNodeElement('actions');
            if ($actionsNodes) {
                foreach ($actionsNodes->children() as $action => $node) {
                    array_push($actions, $action);
                }
            }
            $this->_actions = $actions;
        }
        return $this->_actions;
    }
    /**
     * Check if action enabled for attribute
     * 
     * @param string $action
     * @return boolean
     */
    public function isActionEnabled($action)
    {
        $actions = $this->getActions();
        return (in_array($action, $actions) || in_array('all', $actions)) ? true : false;
    }
    /**
     * Get master attributes
     * 
     * @param string | null $action
     * @return array
     */
    public function getMasterAttributes($action = null)
    {
        if (is_null($this->_masterAttributes)) {
            $masterAttributes = array();
            $masterAttributesActionsNodes = $this->getNodeElement('depends');
            if ($masterAttributesActionsNodes) {
                foreach ($masterAttributesActionsNodes->children() as $_action => $_actionNode) {
                    $attributes = array();
                    if ($_actionNode) {
                        foreach ($_actionNode as $_attribute => $_attributeNode) {
                            array_push($attributes, $_attribute);
                        }
                    }
                    $masterAttributes[$_action] = $attributes;
                }
            }
            $this->_masterAttributes = $masterAttributes;
        }
        if (!is_null($action)) {
            if (isset($this->_masterAttributes['all'])) return $this->_masterAttributes['all'];
            else if (isset($this->_masterAttributes[$action])) return $this->_masterAttributes[$action];
            else return array();
        } else {
            return $this->_masterAttributes;
        }
    }
    /**
     * Check if attribute is master
     * 
     * @param string $attribute
     * @param string $action
     * @return boolean
     */
    public function isMasterAttribute($attribute, $action = 'all')
    {
        $masterAttributes = $this->getMasterAttributes($action);
        if (in_array($attribute, $masterAttributes)) return true;
        else return false;
    }
    /**
     * Get required flags
     * 
     * @param string $action
     * @return array | boolean
     */
    public function getRequired($action = null)
    {
        if (is_null($this->_required)) {
            $required = array();
            $requiredActionsNodes = $this->getNodeElement('required');
            if ($requiredActionsNodes) {
                foreach ($requiredActionsNodes->children() as $_action => $_required) {
                    $required[$_action] = $this->castAsBoolean($_required);
                }
            }
            $this->_required = $required;
        }
        if (!is_null($action)) {
            if (isset($this->_required['all'])) return $this->_required['all'];
            else if (isset($this->_required[$action])) return $this->_required[$action];
            else return false;
        } else {
            return $this->_required;
        }
    }
    /**
     * Check if required
     * 
     * @param string $action
     * @return boolean
     */
    public function isRequired($action = 'all')
    {
        return $this->getRequired($action);
    }
    /**
     * Get validate rules
     * 
     * @return array
     */
    public function getValidateRules()
    {
        if (is_null($this->_validateRules)) {
            $validateRules = array();
            $validateRulesNodes = $this->getNodeElement('validate_rules');
            if ($validateRulesNodes) {
                foreach ($validateRulesNodes->children() as $key => $value) {
                    $validateRules[$key] = $this->castAsString($value);
                }
            }
            $this->_validateRules = $validateRules;
        }
        return $this->_validateRules;
    }
    /**
     * Get text value representation
     * 
     * @param mixed $value
     * @return string
     */
    public function getText($value)
    {
        $text = null;
        $type = $this->getType();
        switch ($type) {
            case 'string' : 
                if (!is_null($value)) $text = $this->castAsString($value);
                break;
            case 'boolean' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsString($this->castAsBoolean($value));
                break;
            case 'integer' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsString($this->castAsInteger($value));
                break;
            case 'float' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsString($this->castAsFloat($value));
                break;
            case 'decimal' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsString($this->castAsDecimal($value, $this->getScale()));
                break;
            case 'amount' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsString($this->castAsDecimal($value, 2));
                break;
            case 'datetime' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsDateTime($value);
                break;
            case 'date' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsDate($value);
                break;
            case 'time' : 
                if (!$this->_isEmpty($value)) $text = $this->castAsTime($value);
                break;
            case 'select' : 
                if (!$this->_isEmpty($value)) $text = $this->castSelectAsString($value);
                break;
            case 'multiselect' : 
                if (!$this->_isEmpty($value)) $text = $this->castMultiSelectAsString($value);
                break;
            default : 
                if (!is_null($value)) $text = $this->castAsString($value);
                break;
        }
        return $text;
    }
    /**
     * Validate
     * 
     * @param mixed $value
     * @return boolean
     */
    protected function _validate($value)
    {
        $isValid = true;
        $type = $this->getType();
        switch ($type) {
            case 'integer': 
                $isValid = (int) $value == $value; 
                break;
            case 'float' : case 'decimal' : case 'amount' : 
                $isValid = (float) $value == $value; 
                break;
            case 'datetime' : case 'date' : case 'time' : 
                $isValid = (strtotime($value)) ? true : false;
                break;
            case 'select' : 
                if (!$this->_isEmpty($value)) $isValid = $this->isOptionValueExists($value);
                break;
            case 'multiselect' : 
                if (!$this->_isEmpty($value)) {
                    $pieces = explode(',', $value);
                    if (count($pieces)) {
                        foreach ($pieces as $piece) {
                            if (!$this->isOptionValueExists($piece)) {
                                $isValid = false; break;
                            }
                        }
                    }
                }
                break;
            default: 
                $isValid = true; 
                break;
        }
        return $isValid;
    }
    /**
     * Cast value
     * 
     * @param mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        $result = null;
        $type = $this->getType();
        switch ($type) {
            case 'string' : 
                if (!is_null($value)) $result = $this->castAsString($value);
                break;
            case 'boolean' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsBoolean($value);
                break;
            case 'integer' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsInteger($value);
                break;
            case 'float' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsFloat($value);
                break;
            case 'decimal' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsDecimal($value, $this->getScale());
                break;
            case 'amount' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsDecimal($value, 2);
                break;
            case 'datetime' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsDateTime($value);
                break;
            case 'date' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsDate($value);
                break;
            case 'time' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsTime($value);
                break;
            case 'select' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsSelect($value);
                break;
            case 'multiselect' : 
                if (!$this->_isEmpty($value)) $result = $this->castAsMultiSelect($value);
                break;
            default : 
                if (!is_null($value)) $result = $this->castAsString($value);
                break;
        }
        return $result;
    }
    /**
     * Validate
     * 
     * @return boolean
     */
    public function validate($value)
    {
        return $this->_validate($value);
    }
    /**
     * Get options tag
     * 
     * @return string
     */
    protected function getOptionsTag()
    {
        return $this->_optionsTag;
    }
    /**
     * Get option tag
     * 
     * @return string
     */
    protected function getOptionTag()
    {
        return $this->_optionTag;
    }
    /**
     * Get option attribute header tag
     * 
     * @return string
     */
    protected function getOptionAttributeHeaderTag()
    {
        return $this->_optionAttributeHeaderTag;
    }
    /**
     * Get option attribute tag
     * 
     * @return string
     */
    protected function getOptionAttributeTag()
    {
        return $this->_optionAttributeTag;
    }
    /**
     * Get tag
     * 
     * @return string
     */
    protected function getTag()
    {
        return $this->_tag;
    }
    /**
     * Get column header tag
     * 
     * @return string
     */
    protected function getColumnHeaderTag()
    {
        return $this->_columnHeaderTag;
    }
    /**
     * Get column tag
     * 
     * @return string
     */
    protected function getColumnTag()
    {
        return $this->_columnTag;
    }
    /**
     * 
     * @param array $options
     * @param int $tabs
     * @param string $id
     * @return string
     */
    protected function _getOptionsHtml($options, $tabs = 0, $id = '')
    {
        $helper = $this->getHelper();
        $html = array();
        $html[] = $this->getHtmlLine('<'.$this->getOptionsTag().(($id) ? ' id="'.$id.'"' : '').' class="entity-attribute-options">', $tabs);
        $html[] = $this->getHtmlLine('<'.$this->getOptionTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getOptionAttributeHeaderTag().'>'.$helper->__('Label').
            '</'.$this->getOptionAttributeHeaderTag().'>', $tabs + 2);
        $html[] = $this->getHtmlLine('<'.$this->getOptionAttributeHeaderTag().'>'.$helper->__('Value').
            '</'.$this->getOptionAttributeHeaderTag().'>', $tabs + 2);
        $html[] = $this->getHtmlLine('</'.$this->getOptionTag().'>', $tabs + 1);
        if (is_array($options) && count($options)) {
            foreach ($options as $option) {
                $optionLabel = $option['label'];
                $optionValue = $option['value'];
                $html[] = $this->getHtmlLine('<'.$this->getOptionTag().'>', $tabs + 1);
                $html[] = $this->getHtmlLine('<'.$this->getOptionAttributeTag().'>'.$optionLabel.'</'.$this->getOptionAttributeTag().'>', $tabs + 2);
                if (is_array($optionValue)) {
                    $html[] = $this->getHtmlLine('<'.$this->getOptionAttributeTag().'>'.$this->_getOptionsHtml($optionValue, $tabs + 3).
                        '</'.$this->getOptionAttributeTag().'>', $tabs + 2);
                } else {
                    $html[] = $this->getHtmlLine('<'.$this->getOptionAttributeTag().'>'.$optionValue.
                        '</'.$this->getOptionAttributeTag().'>', $tabs + 2);
                }
                $html[] = $this->getHtmlLine('</'.$this->getOptionTag().'>', $tabs + 1);
            }
        }
        $html[] = $this->getHtmlLine('</'.$this->getOptionsTag().'>', $tabs);
        return implode($this->getNl(), $html);
    }
    /**
     * Get options html
     * 
     * @param int $tabs
     * @return string
     */
    public function getOptionsHtml($tabs = 0)
    {
        if ($this->isSelectType()) {
            if ($this->hasSource()) {
                $options = $this->getSource()->getOptions();
                return $this->_getOptionsHtml($options, $tabs, $this->getId().'_options');
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    /**
     * Get header html
     * 
     * @param int $tabs
     * @return string
     */
    public function getHeaderHtml($tabs = 0)
    {
        $helper = $this->getHelper();
        $html = array();
        $html[] = $this->getHtmlLine('<'.$this->getTag().'>', $tabs);
        $html[] = $this->getHtmlLine('<'.$this->getColumnHeaderTag().'>'.$helper->__('Attribute').
            '</'.$this->getColumnHeaderTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnHeaderTag().'>'.$helper->__('Type').
            '</'.$this->getColumnHeaderTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnHeaderTag().'>'.$helper->__('Required').
            '</'.$this->getColumnHeaderTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnHeaderTag().'>'.$helper->__('Title').
            '</'.$this->getColumnHeaderTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnHeaderTag().'>'.$helper->__('Description').
            '</'.$this->getColumnHeaderTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('</'.$this->getTag().'>', $tabs);
        return implode($this->getNl(), $html);
    }
    /**
     * Get html
     * 
     * @param string $tabs
     * @param bool $withOptions
     * @return string
     */
    public function getHtml($tabs = 0, $withOptions = false)
    {
        $html = array();
        $description = $this->getDescription();
        if ($withOptions && $this->isShowOptionsInDescription()) $description .= $this->getOptionsHtml($tabs + 2);
        $html[] = $this->getHtmlLine('<'.$this->getTag().' id="'.$this->getId().'" class="entity-attribute">', $tabs);
        $html[] = $this->getHtmlLine('<'.$this->getColumnTag().'>'.$this->getName().'</'.$this->getColumnTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnTag().'>'.$this->getType().'</'.$this->getColumnTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnTag().'>'.implode(', ', $this->getRequired()).'</'.$this->getColumnTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnTag().'>'.$this->getTitle().'</'.$this->getColumnTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('<'.$this->getColumnTag().'>'.$description.'</'.$this->getColumnTag().'>', $tabs + 1);
        $html[] = $this->getHtmlLine('</'.$this->getTag().'>', $tabs);
        return implode($this->getNl(), $html);
    }
}
