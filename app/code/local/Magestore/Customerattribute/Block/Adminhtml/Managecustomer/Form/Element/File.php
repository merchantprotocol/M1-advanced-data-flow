<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Form_Element_File extends Mage_Adminhtml_Block_Customer_Form_Element_File
{
    protected function _getPreviewUrl()
    {   
        if(strstr($this->getValue(),'customer'))
		{
		$str = strstr( $this->getValue(),'/' );
        return Mage::helper('adminhtml')->getUrl('adminhtml/customer/viewfile', array(
            'file'      => Mage::helper('core')->urlEncode($str),
        ));
		}
		else
        return Mage::getBaseUrl('media').'customer_address'.$this->getValue();
//        return Mage::helper('adminhtml')->getUrl('adminhtml/customer/viewfile', array(
//            'image'      => Mage::helper('core')->urlEncode($this->getValue()),
//        ));
    }
    
    
}
?>
