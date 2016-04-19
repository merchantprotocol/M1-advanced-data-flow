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
class Enigma_AdminLogger_Model_Log extends Mage_Core_Model_Abstract{
	const kActionTypeInsert = 'insert';
	const kActionTypeUpdate = 'update';
	const kActionTypeDelete = 'delete';
	const kActionTypeMiscellaneous = 'misc';
	const kActionTypeLogin = 'login';
	
	public function _construct(){
		parent::_construct();
		$this->_init('AdminLogger/Log');
	}
	
	public function getActionTypes(){
		$retour = array();
		$retour[self::kActionTypeInsert ] = mage::helper('AdminLogger')->__(self::kActionTypeInsert );
		$retour[self::kActionTypeUpdate ] = mage::helper('AdminLogger')->__(self::kActionTypeUpdate );
		$retour[self::kActionTypeDelete ] = mage::helper('AdminLogger')->__(self::kActionTypeDelete);
		$retour[self::kActionTypeMiscellaneous ] = mage::helper('AdminLogger')->__(self::kActionTypeMiscellaneous );
		$retour[self::kActionTypeLogin] = mage::helper('AdminLogger')->__(self::kActionTypeLogin );		
		return $retour;	
	}
}