<?php

class Perception_Bannerpro_Block_Adminhtml_Bannerpro_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'bannerpro';
        $this->_controller = 'adminhtml_bannerpro';
        
        $this->_updateButton('save', 'label', Mage::helper('bannerpro')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('bannerpro')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bannerpro_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bannerpro_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bannerpro_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('bannerpro_data') && Mage::registry('bannerpro_data')->getId() ) {
            return Mage::helper('bannerpro')->__("Edit Banner '%s'", $this->htmlEscape(Mage::registry('bannerpro_data')->getTitle()));
        } else {
            return Mage::helper('bannerpro')->__('Add Banner');
        }
    }
}