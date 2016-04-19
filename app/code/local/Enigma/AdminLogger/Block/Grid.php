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
class Enigma_AdminLogger_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct(){
        parent::__construct();
        $this->setId('AdminLoggerTaskGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText($this->__('No items'));
        $this->setDefaultSort('al_date');
        $this->setDefaultDir('DESC');
    }
	
    protected function _prepareCollection(){
        $collection = Mage::getModel('AdminLogger/Log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
    protected function _prepareColumns(){
        $this->addColumn('al_date', array(
            'header'=> Mage::helper('AdminLogger')->__('Date'),
            'index' => 'al_date',
            'type' => 'datetime'
        ));

        $this->addColumn('al_user', array(
            'header'=> Mage::helper('AdminLogger')->__('User'),
            'index' => 'al_user'
        ));

        $this->addColumn('al_object_description', array(
            'header'=> Mage::helper('AdminLogger')->__('Object'),
            'index' => 'al_object_description'
        ));

        $this->addColumn('al_action_type', array(
            'header'=> Mage::helper('AdminLogger')->__('Action'),
            'index' => 'al_action_type',
            'type' => 'options',
            'options' => mage::getModel('AdminLogger/Log')->getActionTypes()
        ));

        $this->addColumn('al_description', array(
            'header'=> Mage::helper('AdminLogger')->__('Description'),
            'index' => 'al_description'
        ));
        return parent::_prepareColumns();
    }

	public function getGridUrl(){
        return '';
    }

    public function getGridParentHtml(){
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    public function getClearUrl(){
    	return $this->getUrl('AdminLogger/Admin/Clear');
    }

    public function getPruneUrl(){
    	return $this->getUrl('AdminLogger/Admin/Prune');
    }
}