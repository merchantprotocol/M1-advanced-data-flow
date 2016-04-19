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
class Magecon_Rma_Block_Adminhtml_Rma_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('adminhtml_rma_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('creation_date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('rma/rma')->getCollection()->addAttributeToFilter('is_deleted', array('eq' => 0));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('rma_id', array(
            'header' => Mage::helper('sales')->__('Refund #'),
            'width' => '80px',
            'index' => 'rma_id',
        ));

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'index' => 'real_order_id',
        ));

        $this->addColumn('creation_date', array(
            'header' => Mage::helper('sales')->__('Created on'),
            'type' => 'datetime',
            'width' => '150px',
            'index' => 'creation_date',
        ));

        $this->addColumn('scan_date', array(
            'header' => Mage::helper('sales')->__('Purchased on'),
            'type' => 'datetime',
            'width' => '150px',
            'index' => 'scan_date',
        ));

        $this->addColumn('client', array(
            'header' => Mage::helper('sales')->__('Client'),
            'type' => 'text',
            'width' => '150px',
            'index' => 'customer_name',
        ));

        $this->addColumn('ship_to', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'ship_to',
        ));


        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'width' => '100px',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('sales')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('sales')->__('Edit'),
                    'url' => array('base' => '*/adminhtml_rma/edit'),
                    'field' => 'rma_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('rma_id');
        $this->getMassactionBlock()->setFormFieldName('rma');

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/adminhtml_rma/edit', array('rma_id' => $row->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}