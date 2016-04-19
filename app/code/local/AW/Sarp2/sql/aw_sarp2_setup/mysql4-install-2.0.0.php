<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'aw_sarp2/profile'
 */
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/profile')}` (
        `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `subscription_engine_code` VARCHAR(255) NOT NULL,
        `reference_id` VARCHAR(255) NOT NULL,
        `customer_id` INT UNSIGNED NULL,
        `status` VARCHAR(255) NOT NULL,
        `amount` FLOAT UNSIGNED NOT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        `start_date` DATE NOT NULL,
        `subscription_type_id` INT UNSIGNED NOT NULL,
        `last_order_id` INT UNSIGNED NULL DEFAULT 0,
        `last_order_date` DATETIME NULL DEFAULT NULL,
        `initial_details` TEXT NOT NULL,
        `details` TEXT NOT NULL,
        PRIMARY KEY (`entity_id`),
        INDEX `fk_aw_sarp2_profile_aw_sarp2_subscription_type_idx` (`subscription_type_id` ASC),
        CONSTRAINT `fk_aw_sarp2_profile_aw_sarp2_subscription_type`
            FOREIGN KEY (`subscription_type_id`)
            REFERENCES `{$installer->getTable('aw_sarp2/subscription_type')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recurring Profile Table';

    CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/profile_order')}` (
        `profile_id` INT UNSIGNED NOT NULL ,
        `order_id` INT UNSIGNED NOT NULL ,
        PRIMARY KEY (`profile_id`, `order_id`) ,
        INDEX `fk_aw_profile_order_aw_profile_idx` (`profile_id` ASC) ,
        CONSTRAINT `fk_aw_profile_order_aw_profile`
            FOREIGN KEY (`profile_id` )
            REFERENCES `{$installer->getTable('aw_sarp2/profile')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Recurring Profile To Order Linkage Table';

    CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/customer_group')}` (
        `profile_id` INT UNSIGNED NOT NULL,
        `customer_id` INT UNSIGNED NOT NULL,
        `group_id` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`profile_id`),
        CONSTRAINT `fk_aw_sarp2_customer_group_aw_sarp2_profile`
            FOREIGN KEY (`profile_id`)
            REFERENCES `{$installer->getTable('aw_sarp2/profile')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Recurring Customer Native Group ID Linkage Table';

    CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/subscription')}` (
        `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `product_id` INT UNSIGNED NOT NULL,
        `is_subscription_only` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
        `move_customer_to_group_id` INT UNSIGNED NULL DEFAULT 0,
        `start_date_code` VARCHAR(255) NOT NULL,
        `day_of_month` INT UNSIGNED NULL DEFAULT 0,
        PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Table';

    CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/subscription_type')}` (
        `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `engine_code` VARCHAR(255) NOT NULL,
        `title` VARCHAR(255) NULL,
        `is_visible` SMALLINT(5) UNSIGNED NOT NULL,
        `store_ids` TEXT NULL,
        `period_length` INT UNSIGNED NOT NULL,
        `period_unit` VARCHAR(255) NOT NULL,
        `period_is_infinite` SMALLINT(5) UNSIGNED NOT NULL,
        `period_number_of_occurrences` INT UNSIGNED NULL,
        `trial_is_enabled` SMALLINT(5) UNSIGNED NOT NULL,
        `trial_number_of_occurrences` INT UNSIGNED NULL,
        `initial_fee_is_enabled` SMALLINT(5) UNSIGNED NOT NULL,
        PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Type Table';

    CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_sarp2/subscription_item')}` (
        `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `subscription_id` INT UNSIGNED NOT NULL,
        `subscription_type_id` INT UNSIGNED NOT NULL,
        `regular_price` FLOAT NOT NULL,
        `trial_price` FLOAT NULL,
        `initial_fee_price` FLOAT NULL,
        `sort_order` INT NULL,
        PRIMARY KEY (`entity_id`),
        INDEX `fk_aw_sarp2_subscription_item_aw_sarp2_subscription_type_idx` (`subscription_type_id` ASC),
        INDEX `fk_aw_sarp2_subscription_type_item_aw_sarp2_subscription_idx` (`subscription_id` ASC),
        CONSTRAINT `fk_aw_sarp2_subscription_item_aw_sarp2_subscription_type`
            FOREIGN KEY (`subscription_type_id`)
            REFERENCES `{$installer->getTable('aw_sarp2/subscription_type')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        CONSTRAINT `fk_aw_sarp2_subscription_type_item_aw_sarp2_subscription`
            FOREIGN KEY (`subscription_id`)
            REFERENCES `{$installer->getTable('aw_sarp2/subscription')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Item Table';
");

$installer->endSetup();