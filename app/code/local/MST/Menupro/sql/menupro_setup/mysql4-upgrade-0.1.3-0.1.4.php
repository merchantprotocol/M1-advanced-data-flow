<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD depth smallint(5) NOT NULL DEFAULT 0
");

$installer->endSetup(); 