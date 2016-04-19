<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Form_Element_Image extends Mage_Adminhtml_Block_Customer_Form_Element_Image
{
    protected function _getPreviewUrl()
    {   
        if (is_array($this->getValue())) {
            return false;
        }
		if(strstr($this->getValue(),'customer'))
		{
	//zend_debug::dump($this->getValue());
        //return Mage::getBaseUrl('media').'customer_address'.$this->getValue();
		$str = strstr( $this->getValue(),'/' );
        return Mage::helper('adminhtml')->getUrl('adminhtml/customer/viewfile', array(
            'image'      => Mage::helper('core')->urlEncode($str),
        ));
		}
		else
		return Mage::getBaseUrl('media').'customer_address'.$this->getValue();
    }
    
    
}
?>
