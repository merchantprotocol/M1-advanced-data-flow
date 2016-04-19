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
class Magecon_Rma_Block_Adminhtml_Rma_Create_New extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_rma_create';
        $this->_blockGroup = 'rma';
        $this->_mode = 'new';
        parent::__construct();
        $this->removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Create RMA'));
    }

    public function getHeaderText() {
        return Mage::helper('rma')->__('New RMA - Order #%s | %s ', Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'))->getRealOrderId(), $this->formatDate(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'))->getCreatedAt(), 'medium', true));
    }

}