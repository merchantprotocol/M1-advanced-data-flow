<?php

class MST_Menupro_Block_Groupmenu extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$groupmenu = Mage::getModel('menupro/groupmenu')->getCollection();
		if ( Mage::registry('groupmenu') ) {
			$this->setCollection(Mage::registry('groupmenu'));
		} else {
			$this->setCollection($groupmenu);
		}
	}
}