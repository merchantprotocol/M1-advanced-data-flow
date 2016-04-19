<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Customerattribute_Block_Customer_Form_Renderer_Multiline extends Magestore_Customerattribute_Block_Customer_Form_Renderer_Abstract
{
    /**
     * Return original entity value
     * Value didn't escape and filter
     *
     * @return array
     */
    public function getValues()
    {
        $value = $this->getEntity()->getData($this->getAttributeObject()->getAttributeCode());
        if (!is_array($value)) {
            $value = explode("\n", $value);
        }
        return $value;
    }

    /**
     * Return count of lines for multiply line attribute
     *
     * @return int
     */
    public function getLineCount()
    {
        return $this->getAttributeObject()->getMultilineCount();
    }

    /**
     * Return array of validate classes
     *
     * @param boolean $withRequired
     * @return array
     */
    protected function _getValidateClasses($withRequired = true)
    {
        $classes    = parent::_getValidateClasses($withRequired);
        $rules      = $this->getAttributeObject()->getValidateRules();
        if (!empty($rules['min_text_length'])) {
            $classes[] = 'validate-length';
            $classes[] = 'minimum-length-' . $rules['min_text_length'];
        }
        if (!empty($rules['max_text_length'])) {
            if (!in_array('validate-length', $classes)) {
                $classes[] = 'validate-length';
            }
            $classes[] = 'maximum-length-' . $rules['max_text_length'];
        }

        return $classes;
    }

    /**
     * Return HTML class attribute value
     * Validate and rules
     *
     * @return string
     */
    public function getLineHtmlClass()
    {
        $classes = $this->_getValidateClasses(false);
        return empty($classes) ? '' : ' ' . implode(' ', $classes);
    }

    /**
     * Return filtered and escaped value
     *
     * @param int $index
     * @return string
     */
    public function getEscapedValue($index)
    {
        $values = $this->getValues();
        if (isset($values[$index])) {
            $value = $values[$index];
        } else {
            $value = '';
        }

        return $this->escapeHtml($this->_applyOutputFilter($value));
    }
}
