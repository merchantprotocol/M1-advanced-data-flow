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
class Magecon_Rma_Block_Adminhtml_Rma_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'rma_id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'rma';
        $this->_mode = 'edit';

        parent::__construct();
        //$this->_updateButton('delete', 'label', Mage::helper('rma')->__('Delete RMA'));
        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save RMA'));
    }

    public function getHeaderText() {
        return Mage::helper('rma')->__('RMA # %s | %s', $this->getRma()->getRmaId(), $this->formatDate($this->getRma()->getCreationDate(), 'medium', true));
    }

    public function getRma() {
        return Mage::registry('sales_rma');
    }

}