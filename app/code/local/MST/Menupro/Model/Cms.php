<?php
class MST_Menupro_Model_Cms extends Mage_Core_Model_Abstract
{
	protected $cms_collection=array();
	
	public function _construct(){
		parent::_construct();
		$this->_init("menupro/cms");
	}
	public function getCmsCollections()
	{
		$cmscollection = Mage::getModel('cms/page')->getCollection();
		foreach ($cmscollection as $value){
			if($value->getIsActive()==true){
				$this->cms_collection[$value->getIdentifier()]=$value->getTitle();
			}
		}
		return $this->cms_collection;
	}
	public function getCmsCollectionsForGrid()
	{
		$cms_for_grid=array();
		$cmscollection = Mage::getModel('cms/page')->getCollection();
		foreach ($cmscollection as $value){
			if($value->getIsActive()==true){
				$cms_for_grid[$value->getIdentifier()]=$value->getTitle();
			}
		}
		return $cms_for_grid;
	}
}