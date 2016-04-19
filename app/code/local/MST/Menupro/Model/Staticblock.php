<?php
class MST_Menupro_Model_Staticblock extends Mage_Core_Model_Abstract
{
	protected $static_block=array();
	public function _construct()
	{
		parent::_construct();
		$this->_init("menupro/staticblock");
	}
	public function getStaticBlockCollections()
	{
		$collection=Mage::getModel("cms/block")->getCollection();
		foreach($collection as $value)
		{
			if($value->getIsActive()==true){
				$this->static_block[$value->getIdentifier()]=$value->getTitle();
			}
		}
		return $this->static_block;
	}
	
	public function getStaticBlockCollectionsForGrid()
	{
		$staticBlockOption=array();
		$collection=Mage::getModel("cms/block")->getCollection();
		foreach($collection as $value)
		{
			if($value->getIsActive()==true){
				$staticBlockOption[$value->getIdentifier()]=$value->getTitle();
			}
		}
		//array_unshift($this->static_block, array("label"=>"--Select static block--","value"=>""));
		return $staticBlockOption;
	}
}