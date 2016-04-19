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
class Magestore_Customerattribute_Block_Customer_Form_Template extends Mage_Core_Block_Abstract
{
    
    protected $_renderBlocks= array();
    
    public function addRenderer($type, $block, $template){
        $this->_renderBlocks[$type] = array(
            'block'=> $block,
            'template' => $template
            );
        return $this;
    }
    
    public function getRenderers(){
        return $this->_renderBlocks;
    }
}