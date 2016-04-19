<?php
class Perception_Bannerpro_Block_Adminhtml_Bannerpro extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_bannerpro';
        $this->_blockGroup = 'bannerpro';
        $this->_headerText = Mage::helper('bannerpro')->__('Banner Manager');
        $this->_addButtonLabel = Mage::helper('bannerpro')->__('Add Banner');
        parent::__construct();
    }
}