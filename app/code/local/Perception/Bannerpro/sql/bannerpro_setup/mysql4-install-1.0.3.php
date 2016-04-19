<?php
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('bannerpro')};
CREATE TABLE {$this->getTable('bannerpro')} (
  `bannerpro_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `image_thumb` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `text` text NULL,
  `effects` varchar(255) NOT NULL default '',
  `sorting_order` int(11) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `weblink` varchar(255) NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`bannerpro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('bannerpro_store')};
CREATE TABLE {$this->getTable('bannerpro_store')} (
  `bannerpro_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`bannerpro_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Profiles to Stores';");

$installer->endSetup(); 