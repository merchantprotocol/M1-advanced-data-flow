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
 
class Enigma_All_Model_TabsSorting {
    public function prepareEnigmaTabs($observer){
        $tabsBlock = $observer->getBlock();
        if($tabsBlock instanceof Mage_Adminhtml_Block_System_Config_Tabs){
            foreach($tabsBlock->getTabs() as $tab){
                if($tab->getId() != 'enigmaall'){
                    continue;
                }
                $_sections = $tab->getSections()->getItems();
                $tab->getSections()->clear();

                $_sectionsLabels = array();
                foreach($_sections as $key => $_section){
                    if(!in_array($key, array('enigmastore','enigmaall'))){
                        $_sectionsLabels[str_replace(' ', '_', $_section->getLabel())] = $_section;
                    }
                }

                ksort($_sectionsLabels);
                foreach($_sectionsLabels as $_section){
                    $tab->getSections()->addItem($_section);
                }

                if(array_key_exists('enigmastore', $_sections)){
                    $tab->getSections()->addItem($_sections['enigmastore']);
                }
                if(array_key_exists('enigmaall', $_sections)){
                    $tab->getSections()->addItem($_sections['enigmaall']);
                }
            }
        }
        return $this;
    }
}