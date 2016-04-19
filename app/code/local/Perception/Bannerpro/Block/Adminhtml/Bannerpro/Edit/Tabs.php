<?php

class Perception_Bannerpro_Block_Adminhtml_Bannerpro_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bannerpro_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bannerpro')->__('Banner Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bannerpro')->__('Banner Information'),
          'title'     => Mage::helper('bannerpro')->__('Banner Information'),
          'content'   => $this->getLayout()->createBlock('bannerpro/adminhtml_bannerpro_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}