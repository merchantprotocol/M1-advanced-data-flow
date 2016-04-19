<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
CHANGE `permission` `permission` VARCHAR( 500 )  NOT NULL DEFAULT '-1'
");

$installer->endSetup(); 