<?php
class MST_Menupro_Model_Refresh extends Mage_Core_Model_Config_Data {

    protected function _afterSave() {
		die('_afterSave');
    }
    
    
    protected function _beforeSave()
    {
    	die('_beforeSave');
    }

}