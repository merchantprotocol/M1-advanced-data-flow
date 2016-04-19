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
class Magecon_Rma_Block_Status extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    public function __construct() {
        $this->_prepareToRender();
        parent::__construct();
    }

    public function _prepareToRender() {

        $this->addColumn('value', array(
            'label' => Mage::helper('adminhtml')->__('Status'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Status');
    }

}