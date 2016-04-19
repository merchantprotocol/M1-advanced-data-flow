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
 
class Enigma_All_Block_Additional_Main extends Mage_Adminhtml_Block_Abstract{
    protected $_plugins = array();

    public function getHeaderText(){
        return $this->__('Additional Info View');
    }

    public function getPluginsHtml(){
        if(count($this->_plugins) == 0){
            $pluginsConfig = Mage::getConfig()->loadModulesConfiguration('enigma_plugin.xml');

            foreach($pluginsConfig->getNode() as $_plugin){
                $_pluginAttributes = $_plugin->asArray();

                if(!array_key_exists('render', $_pluginAttributes) || $_pluginAttributes['render'] == ''){
                    continue;
                }

                if(array_key_exists('active', $_pluginAttributes) && (int)$_pluginAttributes['active'] != 1){
                    continue;
                }

                $_render = $this->getLayout()->createBlock($_pluginAttributes['render']);

                if(!$_render){
                    continue;
                }

                $_sortOrder = 0;
                if(array_key_exists('sort_order', $_pluginAttributes) && (string)$_pluginAttributes['sort_order'] != ''){
                    $_sortOrder = (int)$_pluginAttributes['sort_order'];
                }

                if(array_key_exists($_sortOrder, $this->_plugins)){
                    $_sortOrder = key(asort($this->_plugins)) + 1;
                }
                $this->_plugins[$_sortOrder] = $_render;
            }
        }
        $html = '';
        if(count($this->_plugins) != 0){
            ksort($this->_plugins);
            foreach($this->_plugins as $_render){
                $html.= $_render->toHtml();
            }
        }
        return $html;
    }

    public function getBackButton(){
        $widgetBlock = $this->getLayout()->createBlock('adminhtml/widget');
        $backUrl = $_backUrl = $this->getUrl('adminhtml/system_config/edit', array('section' => 'enigmaall'));

        return $widgetBlock->getButtonHtml($this->__("Back"), "setLocation('{$backUrl}')", "back");
    }
}