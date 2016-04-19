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
 
class Enigma_All_Model_Feed_Extensions extends Enigma_All_Model_Feed_Abstract{
    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl(){
        return Enigma_All_Helper_Config::EXTENSIONS_FEED_URL;
    }

    /**
     * Checks feed
     * @return
     */
    public function check(){
        if(!(Mage::app()->loadCache('enigma_all_extensions_feed')) || (time() - Mage::app()->loadCache('enigma_all_extensions_feed_lastcheck')) > Mage::getStoreConfig('enigmaall/feed/check_frequency')) {
            $this->refresh();
        }
    }

    public function refresh(){
        $exts = array();
        try {
            $Node = $this->getFeedData();
            if (!$Node) return false;
            foreach ($Node->children() as $ext) {
                $exts[(string)$ext->name] = array(
                    'display_name' => (string)$ext->display_name,
                    'version' => (string)$ext->version,
                    'url' => (string)$ext->url
                );
            }
            Mage::app()->saveCache(serialize($exts), 'enigma_all_extensions_feed');
            Mage::app()->saveCache(time(), 'enigma_all_extensions_feed_lastcheck');
            return true;
        } catch (Exception $E) {
            return false;
        }
    }

    public function checkExtensions(){
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);

        $magentoPlatform = Enigma_All_Helper_Versions::getPlatform();
        foreach($modules as $extensionName){
            if(strstr($extensionName, 'Enigma_') === false){
                continue;
            }
            if($extensionName == 'Enigma_Core' || $extensionName == 'Enigma_All'){
                continue;
            }
            if($platformNode = $this->getExtensionPlatform($extensionName)){
                $extensionPlatform = Enigma_All_Helper_Versions::convertPlatform($platformNode);
                if($extensionPlatform < $magentoPlatform){
                    $this->disableExtensionOutput($extensionName);
                }
            }
        }
        return $this;
    }

    public function getExtensionPlatform($extensionName){
        try {
            if($platform = Mage::getConfig()->getNode("modules/$extensionName/platform")){
                $platform = strtolower($platform);
                return $platform;
            } else {
                throw new Exception();
            }
        } catch (Exception $e){
            return false;
        }
    }

    public function disableExtensionOutput($extensionName){
        $coll = Mage::getModel('core/config_data')->getCollection();
        $coll->getSelect()->where("path='advanced/modules_disable_output/$extensionName'");
        $i = 0;
        foreach($coll as $cd){
            $i++;
            $cd->setValue(1)->save();
        }
        if($i == 0){
            Mage::getModel('core/config_data')
                    ->setPath("advanced/modules_disable_output/$extensionName")
                    ->setValue(1)
                    ->save();
        }
        return $this;
    }
}