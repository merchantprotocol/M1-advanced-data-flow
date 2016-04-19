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
$installer = $this; 
$installer->startSetup(); 
$installer->run(" 
CREATE TABLE  {$this->getTable('adminlogger_log')} (
 `al_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `al_date` DATETIME NOT NULL ,
 `al_user` VARCHAR( 25 ) NOT NULL ,
 `al_object_type` VARCHAR( 50 ) NOT NULL ,
 `al_object_id` INT NOT NULL ,
 `al_object_description` VARCHAR( 255 ) NOT NULL ,
 `al_description` TEXT NOT NULL,
 al_action_type varchar(25) NOT NULL 
) ENGINE = MYISAM;
"); 
$installer->endSetup();