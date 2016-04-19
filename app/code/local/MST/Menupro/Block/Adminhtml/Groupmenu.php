<?php

class MST_Menupro_Block_Adminhtml_Groupmenu extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_groupmenu';
        $this->_blockGroup = 'menupro';
        $this->_headerText = Mage::helper('menupro')->__('Manage Menu Group');
        $this->_addButtonLabel = Mage::helper('menupro')->__('Add New Menu Group');
      
        parent::__construct();
    }
}