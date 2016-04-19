<?php
/**
* dasENIGMA.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://codecanyon.net/licenses/regular
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento community edition
* dasENIGMA does not guarantee correct work of this extension
* on any other Magento edition except Magento community edition.
* dasENIGMA does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   Enigma
* @package    Enigma_AdminLogger
* @version    1.0
* @copyright  Copyright (c) 2014 dasENIGMA. (http://codecanyon.net/user/dasEnigma/portfolio?ref=dasEnigma)
* @license    http://codecanyon.net/licenses/regular
*/
class Enigma_AdminLogger_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){    
        $this->_init('AdminLogger/Log', 'al_id');
    }

    public function TruncateTable(){
    	$this->_getWriteAdapter()->delete($this->getMainTable(), "1=1");
    }
    
    public function Prune($delay){
    	$limitTimeStamp = time() - $delay * 3600 * 24;
    	$limitDate = date('Y-m-d', $limitTimeStamp);
    	$this->_getWriteAdapter()->delete($this->getMainTable(), "al_date<'".$limitDate."'");
    }    
}
?>