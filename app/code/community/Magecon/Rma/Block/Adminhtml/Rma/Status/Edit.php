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
class Magecon_Rma_Block_Adminhtml_Rma_Status_Edit extends Magecon_Rma_Block_Adminhtml_Rma_Status_New {

    public function __construct() {
        parent::__construct();
        $this->_mode = 'edit';
        if (Mage::registry('current_status')->canDelete()) {
            $this->_addButton('delete', array(
                'label' => Mage::helper('rma')->__('Delete'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('rma')->__('Are you sure you want to do this?')
                . '\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('rma')->__('Edit RMA Status');
    }

    public function getDeleteUrl() {
        return $this->getUrl('*/adminhtml_rma_status/delete', array('status_id' => Mage::registry('current_status')->getStatusId()));
    }

}