<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('groupmenu')} 
ADD `menu_type` VARCHAR(500) NOT NULL
");

$installer->endSetup(); 