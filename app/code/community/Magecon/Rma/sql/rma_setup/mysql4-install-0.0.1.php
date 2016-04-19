<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_Rma
 * @version    1.0.0
 * @copyright  Copyright (c) 2013 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
$start_value = 100000001;

$this->startSetup();

$this->run("

DROP TABLE IF EXISTS {$this->getTable('magecon_rma')};
CREATE TABLE {$this->getTable('magecon_rma')} (
  `rma_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `real_order_id` int(11) NOT NULL,
  `scan_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(30) NOT NULL,
  `ship_to` text NOT NULL,
  `products` int(10) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modification_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(30) NOT NULL,
  `status_code` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rma_id`)
) AUTO_INCREMENT = {$start_value} ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('magecon_products')};
CREATE TABLE {$this->getTable('magecon_products')} (
  `rp_id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_id` int(11) NOT NULL,
  `condition` text NOT NULL,
  `reason` text NOT NULL,
  `request_type` text NOT NULL,
  `comment` text NOT NULL,
  `product_sku` varchar(64) NOT NULL,
  `product_name` varchar(64) NOT NULL,
  `rma_qty` int(11) NOT NULL DEFAULT '0',
  `action` text NOT NULL,
  `adjustment_fee` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`rp_id`),
  FOREIGN KEY (`rma_id`) references {$this->getTable('magecon_rma')}(rma_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('magecon_history_comments')};
CREATE TABLE {$this->getTable('magecon_history_comments')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` text NOT NULL,
  `notify` boolean NOT NULL,
  `notified` boolean NOT NULL,
  `visible` boolean NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`rma_id`) references {$this->getTable('magecon_rma')}(rma_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('magecon_address')};
CREATE TABLE {$this->getTable('magecon_address')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `second_name` varchar(30) NOT NULL,
  `company` varchar(30) NOT NULL,
  `telephone` varchar(30) NOT NULL,
  `fax` varchar(30) NOT NULL,
  `street` text NOT NULL,
  `city` text NOT NULL,
  `country` text NOT NULL,
  `region` text NOT NULL,
  `post_code` text NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`rma_id`) references {$this->getTable('magecon_rma')}(rma_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('magecon_status')};
CREATE TABLE {$this->getTable('magecon_status')} (
  `status_id` int(15) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,	
  `status` varchar(30) NOT NULL,
  `position` int(10) NOT NULL,
  `email` text NOT NULL,
  `history` text NOT NULL,
  `admin_email` text NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$statusTable = $this->getTable('magecon_status');
$statuses = Mage::getConfig()->getNode('global/sales/rma/statuses')->asArray();
$data = array();
foreach ($statuses as $code => $info) {
    $data[] = array(
        'code' => $code,
        'status' => $info['label'],
        'position' => $info['position']
    );
}
$this->getConnection()->insertArray($statusTable, array('code', 'status', 'position'), $data);

/*
  `status_id` int(5) NOT NULL AUTO_INCREMENT,

  DROP TABLE IF EXISTS {$this->getTable('magecon_rma_shipping_address')};
  CREATE TABLE {$this->getTable('magecon_rma_shipping_address')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rma_id` int(11) NOT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `country_id` char(2) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  FOREIGN KEY (`rma_id`) references {$this->getTable('magecon_rmas')}(rma_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 */
$this->endSetup();
?>
