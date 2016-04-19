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
class Enigma_AdminLogger_AdminController extends Mage_Adminhtml_Controller_Action{
	public function GridAction(){
    	$this->loadLayout();
        $this->renderLayout();
    }
    
    public function ClearAction(){
		Mage::getResourceModel('AdminLogger/Log')->TruncateTable();
    	Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Logs clear'));
    	$this->_redirect('AdminLogger/Admin/Grid');
    }
    
    public function PruneAction(){
		$pruneDelay = mage::getStoreConfig('AdminLogger/general/auto_prune_delay');
		Mage::getResourceModel('AdminLogger/Log')->Prune($pruneDelay);
    	Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Logs successfully pruned'));
    	$this->_redirect('AdminLogger/Admin/Grid');    	
    }
}