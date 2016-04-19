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
class Magestore_Customerattribute_Block_Customer_Form_Renderer_File extends Magestore_Customerattribute_Block_Customer_Form_Renderer_Abstract
{
    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {   
        if ($this->getValue()) {
            return $this->escapeHtml(Mage::helper('core')->urlEncode($this->getValue()));
        }
        return '';
    }
}
