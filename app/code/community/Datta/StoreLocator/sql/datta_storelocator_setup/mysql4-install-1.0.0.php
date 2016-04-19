<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *  
 * 
 * @category    Datta
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  29th Nov,2013 5.10pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     store locatore database script
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
 
$installer = $this;

$installer->startSetup();

/**
 * Create table 'datta_storelocator/store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('datta_storelocator/store'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, 
        array(
                'identity' => true,
                'nullable' => false,
                'primary' => true
        ), 'StoreLocator ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => false
        ), 'Block Title')
    ->addColumn('address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => false
        ), 'StoreLocator Address')
    ->addColumn('zipcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, 
        array(
                'nullable' => false
        ), 'StoreLocator ZipCode')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => false
        ), 'StoreLocator City')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Country')
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, 
        array(
                'nullable' => true
        ), 'StoreLocator Phone')
    ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, 
        array(
                'nullable' => true
        ), 'StoreLocator Fax')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Description')
    ->addColumn('store_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Store Website')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, 
        array(
                'nullable' => false
        ), 'StoreLocator Status')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Image Link')
    ->addColumn('marker', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Marker Link')
    ->addColumn('lat', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Latitude Value')
    ->addColumn('long', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
                'nullable' => true
        ), 'StoreLocator Longitude Value')
    ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, 
        array(), 'StoreLocator Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, 
        array(), 'StoreLocator Modification Time')
    ->setComment('Datta StoreLocator Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'datta_storelocator/stores'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('datta_storelocator/stores'))
    ->addColumn('storelocator_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, 
        array(
                'nullable' => false,
                'primary' => true
        ), 'StoreLocator ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, 
        array(
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
        ), 'Store ID')
    ->addForeignKey(
        $installer->getFkName('datta_storelocator/stores', 'storelocator_id', 
                'datta_storelocator/store', 'entity_id'), 'storelocator_id', 
        $installer->getTable('datta_storelocator/store'), 'entity_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('datta_storelocator/stores', 'store_id', 
                'core/store', 'store_id'), 'store_id', 
        $installer->getTable('core/store'), 'store_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('StoreLocator Store To Store Linkage Table');

$installer->getConnection()->createTable($table);

$installer->endSetup();
