<?php
class MST_Menupro_Block_Adminhtml_Groupmenu_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'groupmenuGrid' );
		$this->setDefaultSort ( 'group_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'menupro/groupmenu' )->getCollection ();
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'group_id', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Group ID' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'group_id' 
		) );
		
		$this->addColumn ( 'title', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Group Title' ),
				'align' => 'left',
				'index' => 'title' 
		) );
		/*
		 * $this->addColumn('description', array( 'header' =>
		 * Mage::helper('menupro')->__('Group Description'), 'align' => 'left',
		 * 'index' => 'description', ));
		 */
		
		/* $this->addColumn ( 'menu_type', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Menu Type' ),
				'align' => 'left',
				'index' => 'menu_type',
				'type' => 'options',
				'options' => array (
						'dropdown' 	=> 'Dropdown',
						'dropline' 	=> 'Dropline',
						'sidebar' 	=> 'Sidebar',
						'accordion' => 'Accordion' 
				) 
		) ); */
		$this->addColumn ( 'position', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Positions' ),
				'align' => 'left',
				'index' => 'position',
				'type' => 'options',
				'options' => array (
						'menu-creator-pro' => 'Top (default)',
						'menu-creator-pro menu-creator-pro-left' => 'Left', 
						'menu-creator-pro menu-creator-pro-right' => 'Right', 
						'menu-creator-pro menu-creator-pro-bottom' => 'Bottom', 
						'menu-creator-pro menu-creator-pro-top-fixed' => 'Top Fixed', 
						'menu-creator-pro menu-creator-pro-left-fixed' => 'Left Fixed', 
						'menu-creator-pro menu-creator-pro-right-fixed' => 'Right Fixed', 
						'menu-creator-pro menu-creator-pro-bottom-fixed' => 'Bottom Fixed',
						'menu-creator-pro menu-creator-pro-bottom-accordion' => 'Accordion', 
				) 
		) );
		
		$this->addColumn ( 'animation', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Animations' ),
				'align' => 'left',
				'index' => 'animation',
				'type' => 'options',
				'options' => array (
						'menu-creator-pro-fade' => 'Fade (default)',
						'menu-creator-pro-scale' => 'Scale', 
						'menu-creator-pro-flip' => 'Flip', 
						'menu-creator-pro-slide' => 'Slide', 
				) 
		) );
		$this->addColumn ( 'responsive', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Responsive' ),
				'align' => 'left',
				'index' => 'responsive',
				'type' => 'options',
				'options' => array (
						'menu-creator-pro-rp-switcher' => 'Responses into switcher (default)',
						'menu-creator-pro-rp-stack' => 'Responses into stack', 
						'menu-creator-pro-rp-icon' => 'Responses into icons', 
						'menu-creator-pro-rp-switcher side-panel' => 'Responses side panel', 
						'disable' => 'No responsiveness',  
				) 
		) );
		
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'status',
				'type' => 'options',
				'options' => array (
						1 => 'Enabled',
						2 => 'Disabled' 
				) 
		) );
		
		$this->addColumn ( 'action', array (
				'header' => Mage::helper ( 'menupro' )->__ ( 'Action' ),
				'width' => '100',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array (
						array (
								'caption' => Mage::helper ( 'menupro' )->__ ( 'Edit' ),
								'url' => array (
										'base' => '*/*/edit' 
								),
								'field' => 'id' 
						) 
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'is_system' => true 
		) );
		
		$this->addExportType ( '*/*/exportCsv', Mage::helper ( 'menupro' )->__ ( 'CSV' ) );
		$this->addExportType ( '*/*/exportXml', Mage::helper ( 'menupro' )->__ ( 'XML' ) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'menu_id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'menupro' );
		
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'menupro' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( '*/*/massDelete' ),
				'confirm' => Mage::helper ( 'menupro' )->__ ( 'Are you sure?' ) 
		) );
		$statuses = Mage::getSingleton ( 'menupro/status' )->getOptionArray ();
		array_unshift ( $statuses, array (
				'label' => '',
				'value' => '' 
		) );
		$this->getMassactionBlock ()->addItem ( 'status', array (
				'label' => Mage::helper ( 'menupro' )->__ ( 'Change status' ),
				'url' => $this->getUrl ( '*/*/massStatus', array (
						'_current' => true 
				) ),
				'additional' => array (
						'visibility' => array (
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper ( 'menupro' )->__ ( 'Status' ),
								'values' => $statuses 
						) 
				) 
		) );
		return $this;
	}
	public function getRowUrl($row) {
		return $this->getUrl ( '*/*/edit', array (
				'id' => $row->getId () 
		) );
	}
}