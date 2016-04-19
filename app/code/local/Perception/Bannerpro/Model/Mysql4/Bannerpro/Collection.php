<?php

class Perception_Bannerpro_Model_Mysql4_Bannerpro_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
 	   protected $_previewFlag;
    
	   public function _construct()
	   {
		parent::_construct();
		$this->_init('bannerpro/bannerpro');
		$this->_map['fields']['bannerpro_id'] = 'main_table.bannerpro_id';
	   }
	
	   public function setFirstStoreFlag($flag = false)
	   {
		$this->_previewFlag = $flag;
		return $this;
	   }

	   protected function _afterLoad()
	   {
		if ($this->_previewFlag) {
		$items = $this->getColumnValues('bannerpro_id');
		
		if (count($items)) {
		$select = $this->getConnection()->select()
		                ->from($this->getTable('bannerpro_store'))
		                ->where($this->getTable('bannerpro_store').'.bannerpro_id IN (?)', $items);
		        
			if ($result = $this->getConnection()->fetchPairs($select)) {

		            foreach ($this as $item) {
		               
				if (!isset($result[$item->getData('bannerpro_id')])) {
		                    continue;
		                }
		                
				if ($result[$item->getData('bannerpro_id')] == 0) {
		                    $stores = Mage::app()->getStores(false, true);
		                    $storeId = current($stores)->getId();
		                    $storeCode = key($stores);
		                } else {
		                    $storeId = $result[$item->getData('bannerpro_id')];
		                    $storeCode = Mage::app()->getStore($storeId)->getCode();
		                }

		                $item->setData('_first_store_id', $storeId);
		                $item->setData('store_code', $storeCode);
		            }
		       }

		    }
		}

		parent::_afterLoad();
	    }
        
    	    /**
	     * Add Filter by store
	     *
	     * @param int|Mage_Core_Model_Store $store
	     * @return Mage_Cms_Model_Mysql4_Page_Collection
	     */
    
	    public function addStoreFilter($store, $withAdmin = true)
	    {
		if (!$this->getFlag('store_filter_added')) {
		    
		    if ($store instanceof Mage_Core_Model_Store) {
		        $store = array($store->getId());
		    }

		    $this->getSelect()->join(
		        array('store_table' => $this->getTable('bannerpro_store')),
		        'main_table.bannerpro_id = store_table.bannerpro_id',
		        array()
		    )->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store))
		    ->group('main_table.bannerpro_id');

		    $this->setFlag('store_filter_added', true);
		}

		return $this;
	     }

		public function prepareSummary()
		{
			$this->setConnection($this->getResource()->getReadConnection());
			$this->getSelect()
			->from(array('main_table'=>'bannerpro'),'*')
			->where('status = ?', 1)
			->order('date','asc');
			return $this;
		}

		public function getDetalle($bannerpro_id)
		{
			$this->setConnection($this->getResource()->getReadConnection());
			$this->getSelect()
			->from(array('main_table'=>'bannerpro'),'*')
			->where('bannerpro_id = ?', $bannerpro_id);
			return $this;
		}
	
		public function getbannerpro()
		{
			$this->setConnection($this->getResource()->getReadConnection());
			$this->getSelect()
			->from(array('main_table'=>'bannerpro'),'*')
			->where('status = ?', 1)
			->order('date DESC')
			->limit(5);
			return $this;
		}

		public function prepareResult($word)
		{	
			$sql = "SELECT title,text FROM bannerpro where title like '%$word%' OR text like '%$word%'";
			$data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);	
			return $data;
		}

}
