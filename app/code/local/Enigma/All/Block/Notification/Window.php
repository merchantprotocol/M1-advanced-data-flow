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
 
class Enigma_All_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Window{
    protected function _construct(){
        parent::_construct();

        if(!Mage::getStoreConfig('enigmaall/install/run')){
            $c = Mage::getModel('core/config_data');
            $c
                    ->setScope('default')
                    ->setPath('enigmaall/install/run')
                    ->setValue(time())
                    ->save();
            $this->setHeaderText($this->__("dasEnigma Notifications Setup"));
            $this->setIsFirstRun(1);
            $this->setIsHtml(1);
        }
    }

    protected function _toHtml(){
        if($this->getIsHtml()){
            $this->setTemplate('enigma_all/notification/window.phtml');
        }
        return parent::_toHtml();
    }

    public function presetFirstSetup(){
    }

    public function getNoticeMessageText(){
        if($this->getIsFirstRun()){
            $child = $this->getLayout()->createBlock('core/template')->setTemplate('enigma_all/notification/window/first-run.phtml')->toHtml();
            return $child;
        } else {
            return $this->getData('notice_message_text');
        }
    }
}