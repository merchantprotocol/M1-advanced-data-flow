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
 
class Enigma_All_Model_Source_Updates_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

    const TYPE_PROMO = 'PROMO';
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_UPDATE_RELEASE = 'UPDATE_RELEASE';
    const TYPE_INFO = 'INFO';
    const TYPE_INSTALLED_UPDATE = 'INSTALLED_UPDATE';

    public function toOptionArray(){
        return array(
            array('value' => self::TYPE_INSTALLED_UPDATE, 'label' => Mage::helper('enigmaall')->__('My extensions updates')),
            array('value' => self::TYPE_UPDATE_RELEASE, 'label' => Mage::helper('enigmaall')->__('All extensions updates')),
            array('value' => self::TYPE_NEW_RELEASE, 'label' => Mage::helper('enigmaall')->__('New Releases')),
            array('value' => self::TYPE_PROMO, 'label' => Mage::helper('enigmaall')->__('Promotions/Discounts')),
            array('value' => self::TYPE_INFO, 'label' => Mage::helper('enigmaall')->__('Other information'))
        );
    }

    /**
     * Retrive all attribute options
     *
     * @return array
     */
    public function getAllOptions(){
        return $this->toOptionArray();
    }

    /**
     * Returns label for value
     * @param string $value
     * @return string
     */
    public function getLabel($value){
        $options = $this->toOptionArray();
        foreach ($options as $v) {
            if ($v['value'] == $value) {
                return $v['label'];
            }
        }
        return '';
    }

    /**
     * Returns array ready for use by grid
     * @return array
     */
    public function getGridOptions(){
        $items = $this->getAllOptions();
        $out = array();
        foreach ($items as $item) {
            $out[$item['value']] = $item['label'];
        }
        return $out;
    }
}