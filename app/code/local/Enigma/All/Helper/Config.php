<?php
/**
 * dasENIGMA.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://codecanyon.net/licenses/regular
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento community edition
 * dasENIGMA does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * dasENIGMA does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Enigma
 * @package    Enigma_All
 * @version    1.1
 * @copyright  Copyright (c) 2014 dasENIGMA. (http://codecanyon.net/user/dasEnigma/portfolio?ref=dasEnigma)
 * @license    http://codecanyon.net/licenses/regular
 */
 
class Enigma_All_Helper_Config extends Mage_Core_Helper_Abstract{
    /** Extensions feed path */
    const EXTENSIONS_FEED_URL = 'http://svn.dasenigma.com/feeds/extensions.xml';
    /** Updates Feed path */
    const UPDATES_FEED_URL = 'http://svn.dasenigma.com/feeds/updates.xml';
    /** Estore URL */
    const STORE_URL = 'http://svn.dasenigma.com/store/';

    /** EStore response cache key*/
    const STORE_RESPONSE_CACHE_KEY = 'enigma_all_store_response_cache_key';
}