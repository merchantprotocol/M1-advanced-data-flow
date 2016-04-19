<?php

class Perception_Bannerpro_Block_Adminhtml_Bannerpro_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('bannerproGrid');
      $this->setDefaultSort('bannerpro_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('bannerpro/bannerpro')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('bannerpro_id', array(
          'header'    => Mage::helper('bannerpro')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'bannerpro_id',
      ));
      
	 $this->addColumn('filename', array(
		'header'=>Mage::helper('bannerpro')->__('Image'),
        'filter'=>false,
        'index'=>'filename',
        'align' => 'left',
        'width'     => '50px',
	    'renderer'  => 'bannerpro/adminhtml_grid_renderer_image',
		)); 
		
	  $this->addColumn('title', array(
          'header'    => Mage::helper('bannerpro')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
	  	  'width'     => '550px',
      ));
	
	$this->addColumn('text', array(
			'header'    => Mage::helper('bannerpro')->__('Description'),
			'align'     =>'left',
			'width'     => '350px',
			'index'     => 'text',
      ));
	  
	$this->addColumn('effects', array(
			'header'    => Mage::helper('bannerpro')->__('Effects'),
			'align'     =>'center',
			'width'     => '50px',
			'index'     => 'effects',
      ));
	  
	  $this->addColumn('weblink', array(
			'header'    => Mage::helper('bannerpro')->__('URL'),
			'align'     =>'center',
			'width'     => '50px',
			'index'     => 'weblink',
      ));
	 
	$this->addColumn('sorting_order', array(
			'header'    => Mage::helper('bannerpro')->__('Sorting Order'),
			'align'     =>'center',
			'width'     => '50px',
			'index'     => 'sorting_order',
      ));

	  if (!Mage::app()->isSingleStoreMode()) {
		$this->addColumn('store_id', array(
			'header'        => Mage::helper('bannerpro')->__('Store View'),
			'index'         => 'store_id',
			'type'          => 'store',
			'width'			=> '150px',
			'store_all'     => true,
			'store_view'    => true,
			'sortable'      => false,
			'filter_condition_callback'
							=> array($this, '_filterStoreCondition'),
		));
	   }
   
      $this->addColumn('status', array(
          'header'    => Mage::helper('bannerpro')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('bannerpro')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('bannerpro')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('bannerpro')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('bannerpro')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('bannerpro_id');
        $this->getMassactionBlock()->setFormFieldName('bannerpro');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('bannerpro')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('bannerpro')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('bannerpro/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('bannerpro')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('bannerpro')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
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
    
	  public function getRowUrl($row)
	  {
	      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	  }

	public function getThumbnailSize()
	{
			$size = trim(Mage::getStoreConfig('bannerpro/info/backend_thumbnail_size'),' ');
			$tmp = explode('-',$size);
			if(sizeof($tmp)==2)
				return array('width'=>is_numeric($tmp[0])?$tmp[0]:85,'height'=>is_numeric($tmp[1])?$tmp[1]:65);
			return array('width'=>85,'height'=>65);
	}
}