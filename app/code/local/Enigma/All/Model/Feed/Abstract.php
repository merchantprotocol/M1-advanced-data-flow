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
class Enigma_All_Model_Feed_Abstract extends Mage_Core_Model_Abstract{
    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    public function getFeedData(){
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
                              'timeout' => 1
                         ));
        $curl->write(Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $data = $curl->read();
        if($data === false){
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }
        return $xml;
    }

    /**
     * Retrieve DB date from RSS date
     *
     * @param string $rssDate
     * @return string YYYY-MM-DD YY:HH:SS
     */
    public function getDate($rssDate){
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }
}