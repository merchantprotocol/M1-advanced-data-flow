<?php
/**
 * Mage Plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mage Plugins Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://mageplugins.net/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mageplugins@gmail.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to https://www.mageplugins.net for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2018 Mage Plugins Inc. and affiliates (https://mageplugins.net/)
 * @license    https://mageplugins.net/commercial-license/  Mage Plugins Commercial License (MPCL 1.0)
 */

/**
 * Config
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Config extends Mage_Core_Model_Config_Base
{
    /**
     * Load config
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCacheId('dataflow_entities_config');
        $this->loadString('<?xml version="1.0"?><config></config>');
        $config = Mage::app()->loadCache($this->getCacheId());
        # TODO remove false to cache
        if (false && $config) {
            $config = new Varien_Simplexml_Config($config);
            $this->extend($config);
            unset($config);
        } else {
            $config = $this->getConfig();
            Mage::getConfig()->loadModulesConfiguration('dataflow.xml', $config);
            $this->extend($config);
            unset($config);
            $this->loadEntities();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($this->getXmlString(), $this->getCacheId(), array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
    }
    /**
     * Get config
     * 
     * @return Varien_Simplexml_Config
     */
    protected function getConfig()
    {
        $config = new Varien_Simplexml_Config();
        $config->loadString('<?xml version="1.0"?><config></config>');
        return $config;
    }
    /**
     * Load entities
     * 
     * @return MP_AdvancedDataflow_Model_Config
     */
    protected function loadEntities()
    {
        if ($this->hasEntities()) {
            foreach ($this->getEntities() as $entityName => $entity) {
                if ((string) $entity->active == 'true') {
                    $config = $this->getConfig();
                    $configFile = 'entities'.DS.$entityName.'.xml';
                    Mage::getConfig()->loadModulesConfiguration($configFile, $config);
                    $this->extend($config, true);
                    unset($config);
                }
            }
        }
        return $this;
    }
    /**
     * Check if has entities
     * 
     * @return bool
     */
    public function hasEntities()
    {
        return $this->getNode('entities')->hasChildren();
    }
    /**
     * Get entities
     * 
     * @return array of SimpleXMLElement
     */
    public function getEntities()
    {
        return $this->getNode('entities')->children();
    }
    /**
     * Get entity
     * 
     * @param string $entityName
     * @return Varien_Simplexml_Element
     */
    public function getEntity($entityName)
    {
        if ($this->hasEntities()) {
            $entities = $this->getEntities();
            if (!empty($entities->$entityName)) {
                return $entities->$entityName;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
