<?php
/**
 * Magento 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *  
 * 
 * @category    Datta
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  2nd Dec,2013 2:20pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Manage store locator grid 
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Datta_StoreLocator_Block_Adminhtml_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid

        $this->setDefaultSort('entity_id');
        $this->setId('StoreLocatorGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'datta_storelocator/store_collection';
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->setFirstStoreFlag(true);
        //$collection = Mage::getModel('datta_storelocator/store')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'align' =>'right',
                'index' => 'entity_id'
            )
        );

        $this->addColumn('image', array(
            'header'    => Mage::helper('datta_storelocator')->__('Image'),
            'width'     => '100',
            'renderer'  => 'datta_storelocator/adminhtml_store_renderer_image',
            'index'     => 'image',
        ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('Name'),
                'index' => 'name',
            )
        );

        $this->addColumn('address',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('Address'),
                'index' => 'address',
            )
        );

        $this->addColumn('zipcode',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('Postal Code'),
                'index' => 'zipcode',
            )
        );

        $this->addColumn('city',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('City'),
                'index' => 'city',
            )
        );

        $this->addColumn('country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'country_id',
        ));

        $this->addColumn('phone',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('Phone'),
                'index' => 'phone',
            )
        );   

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('datta_storelocator')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => false,
                'store_view'    => false,
                'sortable'      => true,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        } 
        
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('datta_storelocator')->__('Status'),
                'index'     => Mage::helper('datta_storelocator')->__('status'),
                'type'      => 'options',
                'options'   => array(
                    0 => Mage::helper('datta_storelocator')->__('Disabled'),
                    1 => Mage::helper('datta_storelocator')->__('Enabled'),
                ),
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('datta_storelocator')->__('CSV'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('datta_storelocator');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('datta_storelocator')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('datta_storelocator')->__('Are you sure?')
        ));

        $statuses = array(1 => Mage::helper('datta_storelocator')->__('Enabled'), 0 => Mage::helper('datta_storelocator')->__('Disabled'));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('datta_storelocator')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('datta_storelocator')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

}