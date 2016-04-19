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
 * @package     Magestore_Shopbybrand
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Shopbybrand Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Shopbybrand
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Block_Adminhtml_Sales_Order_Renderer_Multiselect extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
        $values=explode(',',$row->getData($this->getColumn()->getIndex()));
        $column_index = $this->getColumn()->getData('index');
        $attribute_code = ltrim( $column_index, 'customer_' );
        $attribute = Mage::getModel('customer/attribute')->getCollection()
                        ->addFieldToFilter('attribute_code',$attribute_code)->getFirstItem();
        $options = $attribute->getSource()->getAllOptions(false);
        $label=array();
        foreach($options as $option)
        {       
            foreach($values as $value){
                if($option['value'] == $value)
                    $label[] = $option['label'];
            }                    
        }
        return implode(',',$label);
    }
}