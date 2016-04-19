<?php

class MST_Menupro_Block_Adminhtml_Menupro extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_menupro';
        $this->_blockGroup = 'menupro';
        $_helper = Mage::helper('menupro');
        $this->_headerText = Mage::helper('menupro')->__('');
        $this->_addButtonLabel = Mage::helper('menupro')->__('Add Menu');
        $this->_addButton('mst_reset', array( 'label' => Mage::helper('adminhtml')->__('Add New Menu'), 'class' => 'reset scalable', 'id'=>'reset_menu', 'onclick'=>"location.reload()" ));
        $this->_addButton('save', array( 'label' => Mage::helper('adminhtml')->__('Save Item'), 'class' => 'save scalable', 'id'=>'save_menu', 'onclick'=>"editForm.submit();" ));
        parent::__construct(); 
    }
}