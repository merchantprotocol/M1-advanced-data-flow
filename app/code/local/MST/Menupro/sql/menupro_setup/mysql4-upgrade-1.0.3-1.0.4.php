<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('menupro')} ADD `text_align` VARCHAR(255) DEFAULT 'left';
ALTER TABLE {$this->getTable('groupmenu')} ADD `animation` VARCHAR(255) DEFAULT 'fade';
ALTER TABLE {$this->getTable('groupmenu')} ADD `position` VARCHAR(255) DEFAULT 'menu-creator-pro';
ALTER TABLE {$this->getTable('groupmenu')} ADD `responsive` VARCHAR(255) DEFAULT 'menu-creator-pro-rp-switcher';
ALTER TABLE {$this->getTable('groupmenu')} ADD `color` VARCHAR(255) DEFAULT '';
");

$installer->endSetup(); 