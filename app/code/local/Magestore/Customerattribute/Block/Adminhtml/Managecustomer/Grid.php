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
class Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerattributeGrid');
        $this->setDefaultSort('entity_id');
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
		
        $collection = Mage::getModel('customer/customer')->getCollection()->addNameToSelect()->addAttributeToSelect('*');
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
	protected function _multiSelectFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		$label=$column->getIndex();
		$entityId=array(0);
		$collection1 = Mage::getModel('customer/customer')->getCollection()->addNameToSelect()->addAttributeToSelect('*');
		foreach($this->getCollection() as $collection=>$clloValue)
		{
			$option=$clloValue->getData($label);
			$option=explode(',',$option);
			if(in_array($value,$option))
			{
			$entityId[]=$clloValue->getEntityId();
			}
			
		}
		
		$collection1->addFieldToFilter('entity_id',$entityId);
		$this->setCollection($collection1);
        return $this;
    }
    protected function _prepareColumns()
    {
		
		$i=0;
		$this->addColumn('entity_id', array(
            'header'    => Mage::helper('customerattribute')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));
		$this->addColumn('name', array(
            'header'    => Mage::helper('customerattribute')->__('Name'),
            'align'     =>'left',
            'width'     => '150px',
            'index'     => 'name',
        ));
		$this->addColumn('email', array(
            'header'    => Mage::helper('customerattribute')->__('Email'),
            'align'     =>'left',
            'width'     => '150px',
            'index'     => 'email',
        ));
		$groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customerattribute')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));
		if($i==0){
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$entityTypeId     = $setup->getEntityTypeId('customer');
		$tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
		$shows = Mage::getModel('customer/attribute')->getCollection();
		$shows->getSelect()
                       ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id')->order('sort_order ASC');//->addFieldToFilter('main_table.attribute_id',$customerattributeId);
					   $shows->addFieldToFilter('show_on_grid_customer',1);
		foreach($shows as $show){
		if($show['frontend_input']=='select'){
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> 'options',
			'options'	=> Mage::helper('customerattribute')->getOptions($show['attribute_id']),
            'index'     => $show['attribute_code'],
        ));
		}else if($show['frontend_input']=='multiselect'){
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> 'options',
			'options'	=> Mage::helper('customerattribute')->getOptions($show['attribute_id']),
            'index'     => $show['attribute_code'],
			'renderer' => 'Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Renderer_Multiselect',
			'filter_condition_callback' => array($this, '_multiSelectFilter'),
        ));
		}else if($show['frontend_input']=='image') {
			
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'center',
			'type'		=> 'image',
			'width'		=>60,
            'index'     => $show['attribute_code'],
			'renderer' => 'Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Renderer_Image',
			'filter'	=> false,
							
        ));
		}else if($show['frontend_input']=='boolean'){
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'No',
				1 => 'Yes',
			),
		
            'index'     => $show['attribute_code'],
        ));
		}else if($show['frontend_input']=='file') {
			
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> 'image',
			
            'index'     => $show['attribute_code'],
			'renderer' => 'Magestore_Customerattribute_Block_Adminhtml_Managecustomer_Renderer_File',
			'filter'	=> false,
							
        ));
		}else if($show['frontend_input']=='date') {
			
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> 'datetime',
			'gmtoffset' => true,
		
            'index'     => $show['attribute_code'],
			'format'	=>Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
        ));
		}else {
			$this->addColumn($show['attribute_code'], array(
            'header'    => Mage::helper('customerattribute')->__($show['frontend_label']),
            'align'     =>'left',
			'type'		=> $show['frontend_input'],
		
            'index'     => $show['attribute_code'],
        ));
		}
		}
		$i++;
		}
        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('customerattribute')->__('Action'),
                'width'        => '100px',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('customerattribute')->__('Edit'),
                        'url'        => array('base'=> 'adminhtml/customer/edit'),
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
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customerattribute');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('customerattribute')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('customerattribute')->__('Are you sure?')
        ));
		
		$this->getMassactionBlock()->addItem('subscribe', array(
            'label'        => Mage::helper('customerattribute')->__('Subscribe to Newsletter'),
            'url'        => $this->getUrl('*/*/massSubscribe'),
          
        ));
		
		$this->getMassactionBlock()->addItem('unsubscribe', array(
            'label'        => Mage::helper('customerattribute')->__('Unsubscribe to Newsletter'),
            'url'        => $this->getUrl('*/*/massUnsubscribe'),
           
        ));
		
        $groups = $this->helper('customerattribute')->getGroups();

        array_unshift($groups, array('label'=> '', 'value'=> ''));
        $this->getMassactionBlock()->addItem('assign_group', array(
             'label'        => Mage::helper('customerattribute')->__('Assign a Customer Group'),
             'url'          => $this->getUrl('*/*/massAssignGroup'),
             'additional'   => array(
                'visibility'    => array(
                     'name'     => 'group',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => Mage::helper('customerattribute')->__('Group'),
                     'values'   => $groups
                 )
            )
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
        return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getId()));
    }
}