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

class AW_Sarp2_Block_Adminhtml_Subscription_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aw_sarp2_subscription_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('aw_sarp2/subscription_collection')
            ->joinProductNames()
            ->joinSubscriptionTypes()
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                 'header' => $this->__('ID'),
                 'index'  => 'entity_id',
                 'type'   => 'number',
                 'width'  => '20px',
            )
        );

        $this->addColumn(
            'product_name',
            array(
                 'header' => $this->__('Linked Product'),
                 'index'  => 'product_name',
                 'type'   => 'text',
            )
        );

        $this->addColumn(
            'subscription_type_id',
            array(
                 'header'   => $this->__('Subscription Type'),
                 'index'    => 'subscription_type_id',
                 'type'     => 'options',
                 'options'  => Mage::getResourceModel('aw_sarp2/subscription_type_collection')->toOptionHash(),
                 'width'    => '200px',
                 'renderer' => 'aw_sarp2/adminhtml_widget_grid_column_renderer_multiselect',
                 'sortable' => false,
            )
        );

        $this->addColumn(
            'is_subscription_only',
            array(
                 'header'  => $this->__('Is Subscription Only'),
                 'index'   => 'is_subscription_only',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_yesno')->toArray(),
                 'width'   => '100px',
            )
        );

        $this->addColumn(
            'action',
            array(
                 'header'   => $this->__('Action'),
                 'width'    => '50px',
                 'type'     => 'action',
                 'getter'   => 'getId',
                 'actions'  => array(
                     array(
                         'caption' => $this->__('Edit'),
                         'url'     => array('base' => '*/*/edit'),
                         'field'   => 'id',
                     )
                 ),
                 'filter'   => false,
                 'sortable' => false,
                 'index'    => 'stores',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit/', array('id' => $row->getId()));
    }
}