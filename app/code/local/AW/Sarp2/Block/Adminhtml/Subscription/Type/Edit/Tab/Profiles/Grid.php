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


class AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_Profiles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awsarp2_subscription_type_profiles_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::registry('current_type')
            ->getAssociatedProfileCollection()
            ->addLastOrderDataToSelect()
            ->addCustomerNameToSelect()
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                 'index'        => 'entity_id',
                 'filter_index' => 'main_table.entity_id',
                 'header'       => $this->__('ID'),
                 'width'        => '20px',
            )
        );

        $this->addColumn(
            'reference_id',
            array(
                 'index'  => 'reference_id',
                 'header' => $this->__('Reference ID'),
                 'width'  => '200px',
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

        $engineModel = Mage::helper('aw_sarp2/engine')->getEngineModelByCode(
            Mage::registry('current_type')->getData('engine_code')
        );

        $this->addColumn(
            'status',
            array(
                 'header'   => $this->__('Status'),
                 'index'    => 'status',
                 'type'     => 'options',
                 'options'  => $engineModel->getStatusSource()->toArray(),
                 'width'    => '100px',
                 'sortable' => false,
                 'filter'   => 'aw_sarp2/adminhtml_widget_grid_filter_status',
            )
        );

        $this->addColumn(
            'amount',
            array(
                 'index'         => 'amount',
                 'header'        => $this->__('Billing Amount'),
                 'type'          => 'currency',
                 'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                 'width'         => '80px',
            )
        );

        $this->addColumn(
            'created_at',
            array(
                 'index'  => 'created_at',
                 'header' => $this->__('Date of Profile Creation'),
                 'type'   => 'datetime',
                 'width'  => '150px',
            )
        );

        $this->addColumn(
            'start_date',
            array(
                 'index'  => 'start_date',
                 'header' => $this->__('Date of Start Profile'),
                 'type'   => 'datetime',
                 'width'  => '150px',
            )
        );

        $this->addColumn(
            'last_order_id',
            array(
                'header'       => $this->__('Last Order'),
                'index'        => 'last_order_increment_id',
                'filter_index' => 'order_table.increment_id',
                'width'        => '100px',
                'type'         => 'text',
                'align'        => 'right',
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
                 'index'  => 'last_order_date',
                 'header' => $this->__('Last Order Creation Date'),
                 'type'   => 'datetime',
                 'width'  => '150px',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('aw_recurring_admin/adminhtml_profile/view', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}