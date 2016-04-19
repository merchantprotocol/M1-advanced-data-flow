<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('menupro_license')} (
	license_id int(11) NOT NULL AUTO_INCREMENT,
	domain_count varchar(255) NOT NULL,
	domain_list varchar(255) NOT NULL,
	path varchar(255) NOT NULL,
	extension_code varchar(255) NOT NULL,
	license_key varchar(255) NOT NULL,
	created_time date NOT NULL,
	domains varchar(255) NOT NULL,
	is_valid tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (license_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 