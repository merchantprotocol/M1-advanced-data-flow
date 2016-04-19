<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ALTER permission DROP DEFAULT
");

$installer->endSetup(); 