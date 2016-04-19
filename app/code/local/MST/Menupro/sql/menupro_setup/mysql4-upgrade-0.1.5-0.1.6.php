<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD autosub smallint(6)
");

$installer->endSetup(); 