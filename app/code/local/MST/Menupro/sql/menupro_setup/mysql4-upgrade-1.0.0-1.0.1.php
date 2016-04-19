<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD `hide_sub_header` VARCHAR(5) DEFAULT 2
");

$installer->endSetup(); 