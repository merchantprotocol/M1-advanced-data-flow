<?php
class Perception_Bannerpro_Block_Bannerpro extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBannerpro()     
     { 
        if (!$this->hasData('bannerpro')) {
            $this->setData('bannerpro', Mage::registry('bannerpro'));
        }
        return $this->getData('bannerpro');
    }
    public function getCollection() {
    	$collection = Mage::getModel('bannerpro/bannerpro')->getCollection()
    					->addStoreFilter(Mage::app()->getStore()->getStoreId())
    					->addFieldToFilter('status','1')
						->setOrder('sorting_order','ASC');
    	return $collection;
    }
}
