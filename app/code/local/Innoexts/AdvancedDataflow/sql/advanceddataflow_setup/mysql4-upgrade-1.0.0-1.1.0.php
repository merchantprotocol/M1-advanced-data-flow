<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'real_customer_id', array(
    'label'            => 'Real Customer Id', 
    'visible'          => '0', 
    'required'         => '0', 
    'type'             => 'varchar', 
    'input'            => 'hidden', 
    'sort_order'       => '61', 
));

$installer->endSetup();