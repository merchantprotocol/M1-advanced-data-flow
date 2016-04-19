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

/**
 * Customerattribute Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		if($row->getData($this->getColumn()->getIndex())){
		$fileArray=explode('/',$row->getData($this->getColumn()->getIndex()));
			$html = '<a  target="_blank" id="' . $this->getColumn()->getId() . '"
			href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).
			'customer'.$row->getData($this->getColumn()->getIndex()).'"';
			$html .= '>'.$fileArray[3].'</a>';
		}
		return $html;
    }
}