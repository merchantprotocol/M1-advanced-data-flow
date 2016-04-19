<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('groupmenu')} (
	group_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	status smallint(6) NOT NULL DEFAULT '1',
	created_time datetime DEFAULT NULL,
	update_time datetime DEFAULT NULL,
	PRIMARY KEY (group_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('menupro')} (
	menu_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	group_id int(11) unsigned NOT NULL,
	title varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	image text NOT NULL,
	type text NOT NULL,
	parent_id smallint(6) NOT NULL DEFAULT '0',
	url_value text NOT NULL,
	image_status smallint(6) NOT NULL DEFAULT '1',
	position smallint(6) NOT NULL DEFAULT '1',
	dropdown_columns int(11) NOT NULL DEFAULT '1',
	caption text NOT NULL,
	li_id text NOT NULL,
	class_subfix text NOT NULL,
	other_attribute text NOT NULL,
	permission varchar(256) NOT NULL DEFAULT '1',
	status smallint(6) NOT NULL DEFAULT '1',
	created_time datetime DEFAULT NULL,
	update_time datetime DEFAULT NULL,
	PRIMARY KEY (menu_id),
	FOREIGN KEY (group_id) REFERENCES {$this->getTable('groupmenu')}(group_id)
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup(); 

