<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} ADD `hide_phone` smallint(2) DEFAULT 2;
ALTER TABLE {$this->getTable('menupro')} ADD `hide_tablet` smallint(2) DEFAULT 2;
");

$installer->endSetup(); 