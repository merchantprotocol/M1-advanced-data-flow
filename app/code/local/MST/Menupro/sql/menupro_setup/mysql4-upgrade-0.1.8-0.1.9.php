<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD `icon_class` VARCHAR(500)
");

$installer->endSetup(); 