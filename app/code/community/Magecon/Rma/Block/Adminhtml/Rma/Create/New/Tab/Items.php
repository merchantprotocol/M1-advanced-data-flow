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
class Magecon_Rma_Block_Adminhtml_Rma_Create_New_Tab_Items extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('items_grid');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
        $this->setSkipGenerateContent(true);
        $this->setPagerVisibility(false);
    }

    protected function _prepareCollection() {

        $collection = Mage::getResourceModel('sales/order_item_collection')->setOrderFilter($this->getRequest()->getParam('order_id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('name', array(
            'header' => Mage::helper('sales')->__('Product Name'),
            'width' => '15%',
            'type' => 'text',
            'name' => 'name',
            'align' => 'left',
            'index' => 'name',
            'sortable' => false
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('sales')->__('Sku'),
            'width' => '7%',
            'type' => 'text',
            'name' => 'sku',
            'align' => 'center',
            'index' => 'sku',
            'renderer' => 'Magecon_Rma_Block_Adminhtml_Rma_Create_New_Tab_Renderer_Sku',
            'sortable' => false
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('sales')->__('Qty'),
            'width' => '5%',
            'align' => 'left',
            'index' => 'qty_ordered',
            'renderer' => 'Magecon_Rma_Block_Adminhtml_Rma_Create_New_Tab_Renderer_SelectQty',
            'name' => 'qty[]',
            'editable' => true,
            'sortable' => false
        ));

        if ($this->getConditions()) {
            $this->addColumn('condition[]', array(
                'header' => Mage::helper('sales')->__('Change Item Condition'),
                'width' => '7%',
                'align' => 'left',
                'type' => 'select',
                'options' => $this->getConditions(),
                'name' => 'condition[]',
                'editable' => true,
                'sortable' => false
            ));
        }

        if ($this->getReasons()) {
            $this->addColumn('reason[]', array(
                'header' => Mage::helper('sales')->__('Change Reason'),
                'width' => '7%',
                'align' => 'left',
                'type' => 'select',
                'options' => $this->getReasons(),
                'name' => 'reason[]',
                'editable' => true,
                'sortable' => false
            ));
        }

        if ($this->getRequests()) {
            $this->addColumn('request[]', array(
                'header' => Mage::helper('sales')->__('Change Request'),
                'width' => '7%',
                'align' => 'left',
                'type' => 'select',
                'options' => $this->getRequests(),
                'name' => 'request[]',
                'editable' => true,
                'sortable' => false
            ));
        }
        if (Mage::getStoreConfig('rma/item_attributes_settings/comment')) {
            $this->addColumn('comment[]', array(
                'header' => Mage::helper('sales')->__('Comment'),
                'width' => '36%',
                'type' => 'input',
                'name' => 'comment[]',
                'align' => 'left',
                'sortable' => false
            ));
        }

        return parent::_prepareColumns();
    }

    protected function getConditions() {

        $conditions = unserialize(Mage::getStoreConfig('rma/item_attributes_settings/condition'));
        $conditon_array = array();
        if (!empty($conditions)) {
            $conditon_array['null'] = '';
            foreach ($conditions as $condition) {
                $conditon_array[$condition['value']] = $condition['value'];
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
                $reason_array[$reason['value']] = $reason['value'];
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
                $request_array[$request['value']] = $request['value'];
            }
            return $request_array;
        } else {
            return false;
        }
    }

    protected function isCommentEnabled() {
        if (Mage::getStoreConfig('rma/item_attributes_settings/comment_enable'))
            return true;
        else
            return false;
    }

    public function getRowUrl($row) {
        return '';
    }

}