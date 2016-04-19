<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} 
ADD storeids varchar(250)
");

$installer->endSetup(); 