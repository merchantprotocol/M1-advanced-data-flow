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
class Magecon_Rma_Block_Adminhtml_Rma_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {

        $this->setId('adminhtml_rma_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rma')->__('Customer Information'));
        parent::__construct();
    }

    protected function _beforeToHtml() {

        $this->addTab('address', array(
            'label' => Mage::helper('rma')->__('Customer Address'),
            'content' => $this->getLayout()->createBlock('rma/adminhtml_rma_edit_tab_form')->toHtml(),
        ));

        $this->addTab('items', array(
            'label' => Mage::helper('rma')->__('Items'),
            'content' => $this->getLayout()->createBlock('rma/adminhtml_rma_edit_tab_items')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}