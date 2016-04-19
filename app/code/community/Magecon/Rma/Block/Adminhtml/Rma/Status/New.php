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
class Magecon_Rma_Block_Adminhtml_Rma_Status_New extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'status_id';
        $this->_controller = 'adminhtml_rma_status';
        $this->_blockGroup = 'rma';
        $this->_mode = 'new';

        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save Status'));
        //if ($this->getStatus->canDelete()) {
        $this->_removeButton('delete');
        //}
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('rma')->__('New RMA Status');
    }

}