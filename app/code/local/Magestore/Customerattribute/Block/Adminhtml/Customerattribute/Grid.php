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
class Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerattributeGrid');
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
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$entityTypeId     = $setup->getEntityTypeId('customer');
		$tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
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

		$this->addColumn('show_on_create_account', array(
            'header'    => Mage::helper('customerattribute')->__('Show on <br> Registration page'),
            'align'     =>'center',
            'index'     => 'show_on_create_account',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'No',
				1 => 'Yes',
			),
        ));
		
		$this->addColumn('show_on_account_edit', array(
            'header'    => Mage::helper('customerattribute')->__('Show on <br> Account Manager page'),
            'align'     =>'center',
            'index'     => 'show_on_account_edit',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'No',
				1 => 'Yes',
			),
        ));
		
		$this->addColumn('show_on_checkout_register_customer', array(
            'header'    => Mage::helper('customerattribute')->__('Show on <br> Checkout page(Register)'),
            'align'     =>'center',
            'index'     => 'show_on_checkout_register_customer',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'No',
				1 => 'Yes',
			),
        ));
		
		$this->addColumn('show_on_checkout_register_guest', array(
            'header'    => Mage::helper('customerattribute')->__('Show on <br> Checkout page(Guest)'),
            'align'     =>'center',
            'index'     => 'show_on_checkout_register_guest',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'No',
				1 => 'Yes',
			),
        )); 
		$this->addColumn('frontend_input', array(
            'header'    => Mage::helper('customerattribute')->__('Type'),
            'align'     =>'left',
			'type'		=> 'options',
			'options'	=>array(
				'text'	=>'Text',
				'textarea'	=>'Text Area',
				'date'	=>'Date',
				'boolean'	=>'Yes/No',
				'multiselect'	=>'Multiple Select',
				'select'	=>'Dropdown',
				'image'	=>'Image',
				'file'	=>'File (attachment)',
			),
            'index'     => 'frontend_input',
			
        ));
		
		$this->addColumn('is_custom', array(
            'header'    => Mage::helper('customerattribute')->__('System'),
            'align'     => 'center',
            'width'     => '80px',
            'index'     => 'is_custom',
            'type'        => 'options',
            'options'     => array(
                1 => 'No',
                0 => 'Yes',
            ),
        ));
		
		$this->addColumn('status', array(
            'header'    => Mage::helper('customerattribute')->__('Status'),
            'align'     =>'center',
            'index'     => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Enable',
				2 => 'Disable',
			),
        ));

		$this->addColumn('sort_order', array(
            'header'    => Mage::helper('customerattribute')->__('Sort Order'),
            'width'     => '100px',
			'align'     => 'center',
            'index'     => 'sort_order',
        ));
		
        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('customerattribute')->__('Action'),
                'width'        => '50',
                'type'        => 'action',
                'getter'    => 'getId',
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

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customerattribute_id');
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