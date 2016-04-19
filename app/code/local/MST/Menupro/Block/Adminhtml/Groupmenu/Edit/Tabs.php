<?php

class MST_Menupro_Block_Adminhtml_Groupmenu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('groupmenu_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('menupro')->__('Group Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('menupro')->__('Group Information'),
            'title' => Mage::helper('menupro')->__('Group Information'),
            'content' => $this->getLayout()->createBlock('menupro/adminhtml_groupmenu_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}