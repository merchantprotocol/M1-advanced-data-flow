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
class Magecon_Rma_Block_Adminhtml_Rma_Status_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('adminhtml_rma_status_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('rma/status')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('status', array(
            'header' => Mage::helper('rma')->__('Status'),
            'type' => 'text',
            'index' => 'status',
            'width' => '62%',
        ));
        $this->addColumn('code', array(
            'header' => Mage::helper('rma')->__('Code'),
            'type' => 'text',
            'index' => 'code',
            'width' => '30%',
        ));

        $this->addColumn('position', array(
            'header' => Mage::helper('rma')->__('Position'),
            'type' => 'text',
            'index' => 'position',
            'align' => 'center',
            'width' => '3%',
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('rma')->__('Action'),
            'width' => '5%',
            'type' => 'action',
            'getter' => 'getCode',
            'align' => 'center',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rma')->__('Edit'),
                    'url' => array('base' => '*/adminhtml_rma_status/edit'),
                    'field' => 'code'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/adminhtml_rma_status/edit', array('status_id' => $row->getStatusId()));
    }

}