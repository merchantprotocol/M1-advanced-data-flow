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
 * Customerattribute Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerattribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customerattribute')->__('Customer Attribute Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('customerattribute')->__('General Information'),
            'title'     => Mage::helper('customerattribute')->__('General Information'),
            'content'   => $this->getLayout()
                                ->createBlock('customerattribute/adminhtml_customerattribute_edit_tab_general')
                                ->toHtml(),
        ));
		
		$this->addTab('form_label_section', array(
            'label'     => Mage::helper('customerattribute')->__('Attribute Labels / Options'),
            'title'     => Mage::helper('customerattribute')->__('Attribute Labels / Options'),
            'content'   => $this->getLayout()
                                ->createBlock('customerattribute/adminhtml_customerattribute_edit_tab_options')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}