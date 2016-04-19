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
class Magecon_Rma_Block_Adminhtml_Rma_Status_New_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
        $this->setId('new_rma_status');
    }

    /**
     * Prepare form fields and structure
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {

        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('rma')->__('RMA Status Information')
                ));

        $fieldset->addField('is_new', 'hidden', array('name' => 'is_new', 'value' => 1));

        $fieldset->addField('code', 'text', array(
            'name' => 'code',
            'label' => Mage::helper('rma')->__('Status Code'),
            'class' => 'required-entry',
            'required' => true,
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


        /* $fieldset = $form->addFieldset('store_labels_fieldset', array(
          'legend'       => Mage::helper('sales')->__('Store View Specific Labels'),
          'table_class'  => 'form-list stores-tree',
          ));

          foreach (Mage::app()->getWebsites() as $website) {
          $fieldset->addField("w_{$website->getId()}_label", 'note', array(
          'label'    => $website->getName(),
          'fieldset_html_class' => 'website',
          ));
          foreach ($website->getGroups() as $group) {
          $stores = $group->getStores();
          if (count($stores) == 0) {
          continue;
          }
          $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
          'label'    => $group->getName(),
          'fieldset_html_class' => 'store-group',
          ));
          foreach ($stores as $store) {
          $fieldset->addField("store_label_{$store->getId()}", 'text', array(
          'name'      => 'store_labels['.$store->getId().']',
          'required'  => false,
          'label'     => $store->getName(),
          'value'     => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
          'fieldset_html_class' => 'store',
          ));
          }
          }
          } */

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

        $form->setAction($this->getUrl('*/adminhtml_rma_status/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}