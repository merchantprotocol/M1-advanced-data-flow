<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Model_Permission extends Mage_Core_Model_Abstract
{
	protected $groups=array();
	public function getPermissionCollections()
	{
		$this->groups[]=array('value'=>'-1','label'=>'Public');
		$this->groups[]=array('value'=>'-2','label'=>'Registered');
		$collection = Mage::getModel('customer/group')->getCollection();
		foreach($collection as $value)
		{
			$this->groups[] = array(
					'value'=>$value->getCustomerGroupId(),
					'label' => $value->getCustomerGroupCode()
			);
		}
		return $this->groups;
	}
}