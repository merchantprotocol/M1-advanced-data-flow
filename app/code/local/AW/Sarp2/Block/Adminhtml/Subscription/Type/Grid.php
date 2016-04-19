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

class AW_Sarp2_Block_Adminhtml_Subscription_Type_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awsarp2_subscription_type_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        /**
         * @var AW_Sarp2_Model_Resource_Subscription_Type_Collection $collection
         */
        $collection = Mage::getResourceModel('aw_sarp2/subscription_type_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                 'header' => $this->__('ID'),
                 'index'  => 'entity_id',
                 'width'  => '20px',
            )
        );

        $this->addColumn(
            'engine_code',
            array(
                 'header'  => $this->__('Engine Code'),
                 'index'   => 'engine_code',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_engine')->toArray(),
                 'width'   => '200px',
            )
        );

        $this->addColumn(
            'title',
            array(
                 'header' => $this->__('Title'),
                 'align'  => 'left',
                 'index'  => 'title',
            )
        );

        $this->addColumn(
            'is_visible',
            array(
                 'header'  => $this->__('Status'),
                 'index'   => 'is_visible',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_subscription_type_visibility')->toArray(),
                 'width'   => '100px',
            )
        );

        $this->addColumn(
            'period_length',
            array(
                 'align'  => 'right',
                 'header' => $this->__('Length of Period'),
                 'index'  => 'period_length',
                 'width'  => '100px',
            )
        );

        $this->addColumn(
            'period_unit',
            array(
                 'header'   => $this->__('Period Unit'),
                 'index'    => 'period_unit',
                 'filter'   => false,
                 'sortable' => false,
                 'width'    => '100px',
                 'renderer' => 'AW_Sarp2_Block_Adminhtml_Widget_Grid_Column_Renderer_Unit',
            )
        );

        $this->addColumn(
            'period_is_infinite',
            array(
                 'header'  => $this->__('Is Infinite'),
                 'index'   => 'period_is_infinite',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_yesno')->toArray(),
                 'width'   => '100px',
            )
        );

        $this->addColumn(
            'trial_is_enabled',
            array(
                 'header'  => $this->__('Is Trial Enabled'),
                 'index'   => 'trial_is_enabled',
                 'type'    => 'options',
                 'options' => Mage::getModel('aw_sarp2/source_yesno')->toArray(),
                 'width'   => '100px',
            )
        );

        $this->addColumn(
            'initial_fee_is_enabled',
            array(
                 'header'  => $this->__('Is Initial Fee Enabled'),
                 'index'   => 'initial_fee_is_enabled',
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
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit/', array('id' => $row->getId()));
    }
}