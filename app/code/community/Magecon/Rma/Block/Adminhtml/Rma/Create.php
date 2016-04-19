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
class Magecon_Rma_Block_Adminhtml_Rma_Create extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma_create';
        $this->_blockGroup = 'rma';
        $this->_addButton('back', array(
            'label' => Mage::helper('rma')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class' => 'back',
                ), -1);
        parent::__construct();
        $this->removeButton('delete');
        $this->removeButton('add');
    }

    public function getHeaderText() {
        return Mage::helper('sales')->__('Choose a completed order to create new RMA.');
    }

    public function getBackUrl() {
        return $this->getUrl('*/adminhtml_rma/');
    }

}