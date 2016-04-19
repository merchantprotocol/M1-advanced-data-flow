<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Sarp2_Helper_Engine extends Mage_Core_Helper_Data
{
    const ENGINE_CONFIGURATION_FOLDER = 'etc';
    const ENGINE_CONFIGURATION_FILE = 'engine.xml';

    protected $_enginesConfig = array();

    public function __construct()
    {
        $this->_initEngineConfig();
    }

    protected function _initEngineConfig()
    {
        $configFilePath = Mage::getModuleDir(self::ENGINE_CONFIGURATION_FOLDER, $this->_getModuleName())
            . DS . self::ENGINE_CONFIGURATION_FILE;
        $config = new Varien_Simplexml_Config($configFilePath);
        $this->_enginesConfig = $config->getNode()->asArray();
        uasort($this->_enginesConfig, array($this, '_engineSort'));
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        foreach ($this->_enginesConfig as $key => $value) {
            $optionArray[] = array(
                'value' => $key,
                'label' => $this->__($value['title']),
            );
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->_enginesConfig as $key => $value) {
            $array[$key] = $this->__($value['title']);
        }
        return $array;
    }

    /**
     * Get engine model by code
     *
     * @param string $engineCode
     *
     * @return null|AW_Sarp2_Model_Engine_EngineInterface
     */
    public function getEngineModelByCode($engineCode)
    {
        if (
            array_key_exists($engineCode, $this->_enginesConfig)
            && array_key_exists('model', $this->_enginesConfig[$engineCode])
        ) {
            return Mage::getModel($this->_enginesConfig[$engineCode]['model']);
        }
        return null;
    }

    /**
     * Get engine label by code
     *
     * @param string $engineCode
     *
     * @return null|string
     */
    public function getEngineLabelByCode($engineCode)
    {
        if (
            array_key_exists($engineCode, $this->_enginesConfig)
            && array_key_exists('title', $this->_enginesConfig[$engineCode])
        ) {
            return $this->_enginesConfig[$engineCode]['title'];
        }
        return null;
    }

    public function getEngineWebsiteConfig()
    {
        $engineConfigPerWebsite = array();
        foreach (Mage::app()->getWebsites() as $website) {
            $engineCode = $this->getEngineCodeByWebsiteId($website['website_id']);
            $engineTitle = $engineCode && array_key_exists($engineCode, $this->_enginesConfig)
                ? $this->_enginesConfig[$engineCode]['title'] : $this->__('Subscription Engine not specified')
            ;
            $engineConfigPerWebsite[$website['website_id']] = array(
                'website_code' => Mage::app()->getWebsite($website['website_id'])->getCode(),
                'engine_code'  => $engineCode,
                'engine_title' => $engineTitle,
            );
        }
        return $engineConfigPerWebsite;
    }

    public function getEngineCodeByWebsiteId($websiteId = null)
    {
        return Mage::app()->getWebsite($websiteId)->getConfig('aw_sarp2/settings/subscription_engine');
    }

    public function getWebsitesByEngine(AW_Sarp2_Model_Engine_EngineInterface $engine)
    {
        $assignedWebsites = array();
        foreach (Mage::app()->getWebsites() as $website) {
            if ($engine->getEngineCode() === $this->getEngineCodeByWebsiteId($website['website_id'])) {
                $assignedWebsites[] = $website->getData();
            }
        }
        return $assignedWebsites;
    }

    private function _engineSort($first, $second)
    {
        if ($first['sort_order'] > $second['sort_order']) {
            return 1;
        } elseif ($first['sort_order'] < $second['sort_order']) {
            return -1;
        }
        return 0;
    }

    /**
     * @param $engine AW_Sarp2_Model_Engine_EngineInterface
     *
     * @return array
     */
    public function getPaymentMethodsByEngine($engine)
    {
        if (array_key_exists($engine->getEngineCode(), $this->_enginesConfig)) {
            $engineConfig = $this->_enginesConfig[$engine->getEngineCode()];
            if (array_key_exists('payment_methods', $engineConfig) && count($engineConfig['payment_methods'])) {
                return $engineConfig['payment_methods'];
            }
        }
        return array();
    }
}