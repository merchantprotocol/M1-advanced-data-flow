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
class Magecon_Rma_Block_Adminhtml_Rma_Edit_Tab_Items extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('rma_items_grid');
        $this->setDefaultSort('rp_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
        $this->setSkipGenerateContent(true);
        $this->setPagerVisibility(false);
    }

    protected function getRma() {
        return Mage::registry('sales_rma');
    }

    protected function getRmaId() {
        return $this->getRma()->getRmaId();
    }

    protected function _prepareCollection() {

        $rma_id = $this->getRmaId();
        $collection = Mage::getModel('rma/products')->getCollection()->addFieldToFilter('rma_id', array('eq' => $rma_id))->load();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('name', array(
            'header' => Mage::helper('rma')->__('Product Name'),
            'width' => '15%',
            'type' => 'text',
            'name' => 'name',
            'align' => 'left',
            'index' => 'product_name',
            'sortable' => false
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('rma')->__('Sku'),
            'width' => '7%',
            'type' => 'text',
            'name' => 'sku',
            'align' => 'center',
            'index' => 'product_sku',
            'sortable' => false
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('rma')->__('Qty'),
            'width' => '5%',
            'align' => 'left',
            'index' => 'rma_qty',
            'renderer' => 'Magecon_Rma_Block_Adminhtml_Rma_Edit_Tab_Renderer_SelectQty',
            'name' => 'qty[]',
            'editable' => true,
            'sortable' => false
        ));

        if ($this->getConditions()) {
            $this->addColumn('condition[]', array(
                'header' => Mage::helper('rma')->__('Change Item Condition'),
                'width' => '7%',
                'align' => 'left',
                'index' => 'condition',
                'type' => 'select',
                'options' => $this->getConditions(),
                'name' => 'condition[]',
                'editable' => true,
                'sortable' => false
            ));
        }

        if ($this->getReasons()) {
            $this->addColumn('reason[]', array(
                'header' => Mage::helper('rma')->__('Change Reason'),
                'width' => '7%',
                'align' => 'left',
                'index' => 'reason',
                'type' => 'select',
                'options' => $this->getReasons(),
                'name' => 'reason[]',
                'editable' => true,
                'sortable' => false
            ));
        }

        if ($this->getRequests()) {
            $this->addColumn('request[]', array(
                'header' => Mage::helper('rma')->__('Change Request'),
                'width' => '7%',
                'align' => 'left',
                'index' => 'request_type',
                'type' => 'select',
                'options' => $this->getRequests(),
                'name' => 'request[]',
                'editable' => true,
                'sortable' => false
            ));
        }

        if ($this->isCommentEnabled()) {
            $this->addColumn('comment[]', array(
                'header' => Mage::helper('rma')->__('Comment'),
                'width' => '36%',
                'type' => 'input',
                'name' => 'comment[]',
                'align' => 'left',
                'index' => 'comment',
                'sortable' => false
            ));
        }

        $this->addColumn('action[]', array(
            'header' => Mage::helper('rma')->__('Action'),
            'width' => '7%',
            'type' => 'select',
            'options' => array(Mage::helper('rma')->__('No Action') => Mage::helper('rma')->__('No Action'),
                Mage::helper('rma')->__('Return in stock') => Mage::helper('rma')->__('Return in stock'),
                Mage::helper('rma')->__('Dispose') => Mage::helper('rma')->__('Dispose')),
            'name' => 'action[]',
            'align' => 'left',
            'index' => 'action',
            'sortable' => false
        ));

        return parent::_prepareColumns();
    }

    protected function getConditions() {

        $conditions = unserialize(Mage::getStoreConfig('rma/item_attributes_settings/condition'));
        $conditon_array = array();
        if (!empty($conditions)) {
            $conditon_array['null'] = '';
            foreach ($conditions as $condition) {
                if (trim($condition['value']) != '') {
                    $conditon_array[$condition['value']] = $condition['value'];
                }
            }
            return $conditon_array;
        } else {
            return false;
        }
    }

    protected function getReasons() {

        $reasons = unserialize(Mage::getStoreConfig('rma/item_attributes_settings/reason'));
        $reason_array = array();
        if (!empty($reasons)) {
            $reason_array['null'] = '';
            foreach ($reasons as $reason) {
                if (trim($reason['value']) != '') {
                    $reason_array[$reason['value']] = $reason['value'];
                }
            }
            return $reason_array;
        } else {
            return false;
        }
    }

    protected function getRequests() {

        $requests = unserialize(Mage::getStoreConfig('rma/item_attributes_settings/request'));
        $request_array = array();
        if (!empty($requests)) {
            $request_array['null'] = '';
            foreach ($requests as $request) {
                if (trim($request['value']) != '') {
                    $request_array[$request['value']] = $request['value'];
                }
            }
            return $request_array;
        } else {
            return false;
        }
    }

    protected function isCommentEnabled() {
        return Mage::getStoreConfig('rma/item_attributes_settings/comment');
    }

    public function getRowUrl($row) {
        return '';
    }

}