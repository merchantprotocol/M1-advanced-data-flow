<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('menupro')} 
ADD target smallint(6) DEFAULT 1
");

$installer->endSetup(); 