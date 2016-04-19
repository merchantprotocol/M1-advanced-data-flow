<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_Rma
 * @version    1.0.0
 * @copyright  Copyright (c) 2013 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_Rma_Block_Adminhtml_Rma_Edit_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _prepareLayout() {
        /* $onclick = "submitAndReloadArea($('rma_history_block').parentNode, '".$this->getSubmitUrl()."')";
          $button = $this->getLayout()->createBlock('adminhtml/widget_button')
          ->setData(array(
          'label'   => Mage::helper('rma')->__('Submit Comment'),
          'class'   => 'save',
          'onclick' => $onclick
          ));
          $this->setChild('submit_button', $button); */
        return parent::_prepareLayout();
    }

    public function getRma() {
        return Mage::registry('sales_rma');
    }

    public function getSubmitUrl() {
        return $this->getUrl('*/*/addComment', array('rma_id' => $this->getRma()->getRmaId()));
    }

    public function getCustomerViewUrl() {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $this->getRma()->getCustomerId()));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel() {
        return Mage::helper('rma')->__('Request Information');
    }

    public function getTabTitle() {
        return Mage::helper('rma')->__('Request Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}