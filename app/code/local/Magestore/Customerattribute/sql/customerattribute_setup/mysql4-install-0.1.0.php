<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create customerattribute table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('customerattribute')};

CREATE TABLE {$this->getTable('customerattribute')} (
  `customerattribute_id` int(11) unsigned NOT NULL auto_increment,
  `attribute_id` int(11) NOT NULL,
  `status` smallint(6) NOT NULL default '1' ,
  `customer_group` varchar(255) NOT NULL  ,
  `show_on_create_account` smallint(6) NOT NULL,
  `show_on_account_edit` smallint(6) NOT NULL,
  `show_on_checkout_register_customer` smallint(6) NOT NULL,
  `show_on_checkout_register_guest` smallint(6) NOT NULL,
  `show_on_grid_customer` smallint(6) NOT NULL,
  `show_on_grid_order` smallint(6) NOT NULL,
  `show_on_admin_checkout` smallint(6) NOT NULL,
  `store_id` varchar(255) NOT NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `is_custom` smallint(6) NOT NULL,
  PRIMARY KEY (`customerattribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('customeraddressattribute')};
    
CREATE TABLE {$this->getTable('customeraddressattribute')} (
  `customeraddressattribute_id` int(11) unsigned NOT NULL auto_increment,
  `attribute_id` int(11) NOT NULL ,
  `status` smallint(6) NOT NULL default '1',
  `show_on_checkout` smallint(6) NOT NULL ,
  `show_on_acount_address` smallint(6) NOT NULL ,
  `display_on_backend` smallint(6) NOT NULL ,
  `store_id` varchar(255) NOT NULL,
  `is_custom` smallint(6) NOT NULL,
  PRIMARY KEY (`customeraddressattribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('orderattribute')};
    
CREATE TABLE  {$this->getTable('orderattribute')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL ,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");



/* Add Customer System Attribute to Table  Create by Thinhnd */
$enity_customer_id = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer');
$customer_attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($enity_customer_id);
foreach ($customer_attributes as $customer_attribute)
{
	$attribute      = Mage::getSingleton('eav/config')->getAttribute('customer', $customer_attribute['attribute_code']);
        $getUsedInForms = $attribute->getUsedInForms();
        $data_CA = array('attribute_id'=>$customer_attribute->getAttributeId());
	$data_CA['show_on_create_account'] = (int)in_array('customer_account_create',$getUsedInForms);
	$data_CA['show_on_account_edit'] = (int)in_array('customer_account_edit',$getUsedInForms);
	$data_CA['show_on_checkout_register_customer'] = (int)in_array('checkout_register',$getUsedInForms);
	$data_CA['show_on_checkout_register_guest'] = (int)in_array('checkout_register',$getUsedInForms);
	$data_CA['show_on_grid_customer'] = (int)in_array('show_on_grid_customer',$getUsedInForms);
	$data_CA['show_on_grid_order'] = (int)in_array('show_on_grid_order',$getUsedInForms);
	$data_CA['show_on_admin_checkout'] = (int)in_array('adminhtml_checkout',$getUsedInForms);
	//$data_CA['show_on_backend'] = $show_backend;
	$data_CA['customer_group'] = implode(',', array(1,2,3,4) );
	$data_CA['store_id'] = 0;
	$data_CA['status'] = 1;
    Mage::getModel('customerattribute/customerattribute')->setData($data_CA)->save();
}

/* Add Address System Attribute to Table  Create by Hoatq */
$enity_address_id = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer_address');
$address_attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($enity_address_id);
foreach ($address_attributes as $address_attribute)
{   
    $attributes      = Mage::getSingleton('eav/config')->getAttribute('customer_address', $address_attribute['attribute_code']);
    $getUsedInForm = $attributes->getUsedInForms();   
    $data = array(
        'attribute_id'=>$address_attribute->getAttributeId(),
        'show_on_checkout'  => (int)in_array('customer_register_address',$getUsedInForm),
        'show_on_acount_address' =>(int)in_array('customer_address_edit',$getUsedInForm),
        'store_id'  =>0,
        'display_on_backend'=>1,
        'status'    =>1,
        'is_custom' =>0,
            );
    Mage::getModel('customerattribute/customeraddressattribute')->setData($data)->save();
}

/*  Add Column Attribute to Orderattribute Table Create by Hoatq  */
$order_customer_id = Mage::getModel('customer/entity_setup', 'core_setup')->getEntityTypeId('customer');
$order_attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($order_customer_id);
foreach ($order_attributes as $order_attribute)
{
    $column_name = 'customer_'.$order_attribute['attribute_code'];
    $installer->getConnection()->addColumn($installer
        ->getTable('orderattribute'), $column_name, 'varchar(255)');
}
$installer->endSetup();

