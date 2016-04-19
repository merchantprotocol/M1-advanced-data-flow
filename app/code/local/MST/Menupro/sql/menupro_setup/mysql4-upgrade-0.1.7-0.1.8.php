<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD `custom_width` VARCHAR(100)
");

$installer->endSetup(); 