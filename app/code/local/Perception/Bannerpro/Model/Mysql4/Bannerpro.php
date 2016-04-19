<?php

class Perception_Bannerpro_Model_Mysql4_Bannerpro extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannerpro_id refers to the key field in your database table.
        $this->_init('bannerpro/bannerpro', 'bannerpro_id');
    }
    
    	 /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
    	
        if (! $object->getId() && $object->getCreationTime() == "") {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        if ($date = $object->getData('creation_time')) {
	        $object->setData('creation_time', Mage::app()->getLocale()->date($date, $format, null, false)
    		        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
        );
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        
        return $this;
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('bannerpro_store'))
            ->where('bannerpro_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }    
    
    /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('bannerpro_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('bannerpro_store'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['bannerpro_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('bannerpro_store'), $storeArray);
        }

        return parent::_afterSave($object);
    }    
    
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(
                        array('bannerpro' => $this->getTable('bannerpro_store')),
                        $this->getMainTable().'.bannerpro_id = `bannerpro`.bannerpro_id'
                    )
                    ->where('is_active=1 AND `bannerpro`.store_id in (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $object->getStoreId())
                    ->order('store_id DESC')
					->limit(1);
        }
        return $select;
    }
    
    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($bannerpro_id, $storeId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'bannerpro_id')
            ->join(
                array('bannerpro' => $this->getTable('bannerpro_store')),
                'main_table.bannerpro_id = `bannerpro`.bannerpro_id'
            )
            ->where('main_table.bannerpro_id=?', $bannerpro_id)
            ->where('main_table.is_active=1 AND `bannerpro`.store_id in (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
            ->order('store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }    
    
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array

     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
            ->from($this->getTable('bannerpro_store'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }
	   
    
}