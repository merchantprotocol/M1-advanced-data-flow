<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customerattribute Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Block_Adminhtml_Customeraddressattribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    public function __construct()
    {   
        parent::__construct();
        $this->setId('customeraddressattributeGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareCollection()
    {   
        $entityTypeId = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer_address');
        $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customeraddressattribute');
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId);         
        $collection->getSelect()
            ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareColumns()
    {   
        $this->addColumn('attribute_code', array(
            'header'    => Mage::helper('customerattribute')->__('Attribute Code'),
            'align'     =>'left',
            'index'     => 'attribute_code',
        ));
        $this->addColumn('frontend_label', array(
            'header'    => Mage::helper('customerattribute')->__('Attribute Label'),
            'align'     =>'left',
            'index'     => 'frontend_label',
        ));        
        $this->addColumn('show_on_checkout', array(
            'header'    => Mage::helper('customerattribute')->__('Show on checkout page'),
            'align'     =>'center',
            'width'     => '150px',
            'index'     => 'show_on_checkout',
            'type'	=> 'options',
            'options'	=> array(
                                0 => 'No',
                                1 => 'Yes',
                                 ),
        )); 
        $this->addColumn('show_on_acount_address', array(
            'header'    => Mage::helper('customerattribute')->__('Show on Account Manager page'),
            'align'     =>'center',
            'width'     => '150px',
            'index'     => 'show_on_acount_address',
            'type'	=> 'options',
            'options'	=> array(
                                0 => 'No',
                                1 => 'Yes',
                                 ),
        )); 
        $this->addColumn('frontend_input', array(
            'header'    => Mage::helper('customerattribute')->__('Type'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'frontend_input',
        )); 
       $this->addColumn('is_custom', array(
            'header'    => Mage::helper('customerattribute')->__('System'),
            'align'     =>'center',
            'width'     => '100px',
            'index'     => 'is_custom',
            'type'	=> 'options',
            'options'	=> array(
                                0 => 'Yes',
                                1 => 'No',
                                 ),
        ));        
       $this->addColumn('status', array(
            'header'    => Mage::helper('customerattribute')->__('Status'),
            'align'     =>'center',
            'width'     => '100px',
            'index'     => 'status',
            'type'	=> 'options',
            'options'	=> array(
                                2 => 'Disable',
                                1 => 'Enable',
                                 ),
        ));
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('customerattribute')->__('Sort Order'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'sort_order',
        ));  
        
        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('customerattribute')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getAttributeId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('customerattribute')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customerattribute')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customerattribute')->__('XML'));
        return $this;
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customeraddressattribute_id');
        $this->getMassactionBlock()->setFormFieldName('attribute_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('customerattribute')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('customerattribute')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('customerattribute/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('customerattribute')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('customerattribute')->__('Status'),
                    'values'=> $statuses
                ))
        ));
        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getAttributeId()));
    }
}