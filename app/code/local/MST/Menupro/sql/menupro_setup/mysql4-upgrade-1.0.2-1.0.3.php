<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD `use_category_title` VARCHAR(5) DEFAULT 2
");

$installer->endSetup(); 