<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Sarp2_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awsarp2_profile_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $profileCollection = Mage::getResourceModel('aw_sarp2/profile_collection')
            ->addCustomerNameToSelect()
            ->addLastOrderDataToSelect()
        ;
        $this->setCollection($profileCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                 'header'       => $this->__('ID'),
                 'index'        => 'entity_id',
                 'filter_index' => 'main_table.entity_id',
                 'type'         => 'number',
                 'width'        => '20px',
            )
        );

        $this->addColumn(
            'reference_id',
            array(
                 'header' => $this->__('Gateway ID'),
                 'index'  => 'reference_id',
                 'width'  => '150px',
            )
        );

        $this->addColumn(
            'customer_fullname',
            array(
                'header'   => $this->__('Customer'),
                'index'    => 'customer_fullname',
                'renderer' => 'aw_sarp2/adminhtml_widget_grid_column_renderer_customer',
            )
        );

        $this->addColumn(
            'subscription_engine_code',
            array(
                 'header'  => $this->__('Engine Code'),
                 'index'   => 'subscription_engine_code',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_engine')->toArray(),
                 'width'   => '150px',
            )
        );

        $this->addColumn(
            'status',
            array(
                 'header'                    => $this->__('Status'),
                 'index'                     => 'status',
                 'type'                      => 'options',
                 'options'                   => Mage::getModel('aw_sarp2/source_profile_status')->toArray(),
                 'width'                     => '100px',
                 'sortable'                  => false,
                 'renderer'                  => 'aw_sarp2/adminhtml_widget_grid_column_renderer_status',
                 'filter'                    => 'aw_sarp2/adminhtml_widget_grid_filter_status',
                 'filter_condition_callback' => array(
                     Mage::getResourceModel('aw_sarp2/profile_collection'), 'addStatusFilterCallback'
                 )
            )
        );

        $this->addColumn(
            'amount',
            array(
                 'header'        => $this->__('Billing Amount'),
                 'index'         => 'amount',
                 'type'          => 'currency',
                 'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            )
        );

        $this->addColumn(
            'created_at',
            array(
                 'header'       => $this->__('Created At'),
                 'index'        => 'created_at',
                 'filter_index' => 'main_table.created_at',
                 'type'         => 'datetime',
                 'width'        => '150px',
            )
        );

        $this->addColumn(
            'start_date',
            array(
                 'header' => $this->__('Start Date'),
                 'index'  => 'start_date',
                 'type'   => 'date',
            )
        );

        $this->addColumn(
            'last_order_id',
            array(
                 'header'       => $this->__('Last Order'),
                 'index'        => 'last_order_increment_id',
                 'filter_index' => 'order_table.increment_id',
                 'width'        => '120px',
                 'type'         => 'text',
                 'renderer'     => 'aw_sarp2/adminhtml_widget_grid_column_renderer_link',
            )
        );

        $this->addColumn(
            'last_order_base_grand_total',
            array(
                'header'        => $this->__('Last Order Grand Total'),
                'index'         => 'last_order_base_grand_total',
                'filter_index'  => 'order_table.base_grand_total',
                'width'         => '80px',
                'type'          => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            )
        );

        $this->addColumn(
            'last_order_date',
            array(
                 'header' => $this->__('Last Order Date'),
                 'index'  => 'last_order_date',
                 'type'   => 'datetime',
                 'width'  => '150px',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('aw_recurring_admin/adminhtml_profile/view/', array('id' => $row->getId()));
    }
}