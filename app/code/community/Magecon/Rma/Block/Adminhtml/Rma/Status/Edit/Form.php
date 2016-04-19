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
class Magecon_Rma_Block_Adminhtml_Rma_Status_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
        $this->setId('new_rma_status');
    }

    protected function _prepareForm() {
        $model = Mage::registry('current_status');
        //$labels = $model ? $model->getStoreLabels() : array();

        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('rma')->__('RMA Status Information')
                ));

        $fieldset->addField('code', 'text', array(
            'name' => 'code',
            'label' => Mage::helper('rma')->__('Status Code'),
            'readonly' => true,
                )
        );

        $fieldset->addField('status', 'text', array(
            'name' => 'status',
            'label' => Mage::helper('rma')->__('Status Label'),
            'class' => 'required-entry',
            'required' => true,
                )
        );

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('rma')->__('Position'),
            'class' => 'required-entry',
            'required' => true,
                )
        );

        $information = $form->addFieldset('information', array(
            'legend' => Mage::helper('rma')->__('RMA Status Notifications')
                ));

        $information->addField('email', 'textarea', array(
            'name' => 'email',
            'label' => Mage::helper('rma')->__('Customer email notification'),
                )
        );

        $information->addField('history', 'textarea', array(
            'name' => 'history',
            'label' => Mage::helper('rma')->__('Comment history notification'),
                )
        );

        $information->addField('admin_email', 'textarea', array(
            'name' => 'admin_email',
            'label' => Mage::helper('rma')->__('Admin email notification'),
                )
        );

        if ($model) {
            $form->addValues($model->getData());
        }
        $form->setAction(
                $this->getUrl('*/adminhtml_rma_status/save', array('status_id' => $this->getRequest()->getParam('status_id')))
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}