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
 
class Enigma_All_Model_Feed_Updates extends Enigma_All_Model_Feed_Abstract{
    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl(){
        return Enigma_All_Helper_Config::UPDATES_FEED_URL;
    }

    /**
     * Checks feed
     * @return
     */
    public function check(){
        if((time() - Mage::app()->loadCache('enigma_all_updates_feed_lastcheck')) > Mage::getStoreConfig('enigmaall/feed/check_frequency')){
            $this->refresh();
        }
    }

    public function refresh(){
        $feedData = array();

        try {
            $Node = $this->getFeedData();
            if(!$Node) return false;
            foreach ($Node->children() as $item){

                if($this->isInteresting($item)){
                    $date = strtotime((string)$item->date);
                    if(!Mage::getStoreConfig('enigmaall/install/run') || (Mage::getStoreConfig('enigmaall/install/run') < $date)){
                        $feedData[] = array(
                            'severity' => 3,
                            'date_added' => $this->getDate((string)$item->date),
                            'title' => (string)$item->title,
                            'description' => (string)$item->content,
                            'url' => (string)$item->url,
                        );
                    }
                }
            }

            $adminnotificationModel = Mage::getModel('adminnotification/inbox');
            if($feedData && is_object($adminnotificationModel)){
                $adminnotificationModel->parse(($feedData));
            }
            Mage::app()->saveCache(time(), 'enigma_all_updates_feed_lastcheck');
            return true;
        } catch (Exception $E) {
            return false;
        }
    }

    public function getInterests(){
        if(!$this->getData('interests')){
            $types = @explode(',', Mage::getStoreConfig('enigmaall/feed/interests'));
            $this->setData('interests', $types);
        }
        return $this->getData('interests');
    }

    /**
     *
     * @return
     */
    public function isInteresting($item){
        $interests = $this->getInterests();

        $types = @explode(",", (string)$item->type);
        $exts = @explode(",", (string)$item->extensions);

        $isInterestedInSelfUpgrades = array_search(Enigma_All_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE, $types);

        foreach($types as $type){
            if(array_search($type, $interests) !== false){
                return true;
            }
            if(($type == Enigma_All_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE) && $isInterestedInSelfUpgrades) {
                foreach ($exts as $ext) {
                    if ($this->isExtensionInstalled($ext)){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isExtensionInstalled($code){
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());

        foreach($modules as $moduleName){
            if($moduleName == $code){
                return true;
            }
        }
        return false;
    }
}