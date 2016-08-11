<?php
/**
 * Merchant Protocol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Merchant Protocol Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://merchantprotocol.com/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@merchantprotocol.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.merchantprotocol.com for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2016 Merchant Protocol LLC. and affiliates (https://merchantprotocol.com/)
 * @license    https://merchantprotocol.com/commercial-license/  Merchant Protocol Commercial License (MPCL 1.0)
 */

/**
 * Entity adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * Entity
     * 
     * @var Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected $_entity;
    /**
     * Get helper
     * 
     * @return Innoexts_AdvancedDataflow_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('advanceddataflow');
    }
    /**
     * Set entity
     * 
     * @param Innoexts_AdvancedDataflow_Model_Entity_Entity $entity
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity
     */
    protected function setEntity(Innoexts_AdvancedDataflow_Model_Entity_Entity $entity)
    {
        $this->_entity = $entity;
        return $this;
    }
    /**
     * Get entity
     * 
     * @return Innoexts_AdvancedDataflow_Model_Entity_Entity
     */
    protected function getEntity()
    {
        if (is_null($this->_entity)) {
            $entity = Mage::getModel($this->getEntityModelName(), array('name' => $this->getEntityName()));
            $this->_entity = $entity;
        }
        return $this->_entity;
    }
    /**
     * Get entity type
     * 
     * @return string
     */
    protected function getEntityType()
    {
        return $this->getVar('entity_type');
    }
    /**
     * Get entity model name
     * 
     * @return string
     */
    protected function getEntityModelName()
    {
        return $this->getVar('entity_model', 'advanceddataflow/entity_entity');
    }
    /**
     * Get entity name
     * 
     * @return string
     */
    protected function getEntityName()
    {
        $name = $this->getEntityType();
        return str_replace('/', '_', $name);
    }
    /**
     * Load collection identifiers
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity
     */
    public function load()
    {
        $helper = $this->getHelper();
        $entityIds = array();
        try {
            $entity = $this->getEntity();
            $filters = array();
            $entityIds = $entity->getIdsByFilters($filters);
            $message = $helper->__('Loaded %d records', count($entityIds));
            $this->addException($message);
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = $helper->__('Problem loading the collection, aborting. Error: %s', $e->getMessage());
            $this->addException($message, Varien_Convert_Exception::FATAL);
        }
        $this->setData($entityIds);
        return $this;
    }
    /**
     * Save
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity
     */
    public function save()
    {
        return $this;
    }
    /**
     * Save row
     * 
     * @param array $data
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity
     */
    public function saveRow($data)
    {
        $this->getEntity()->saveModelData($data);
        return $this;
    }
}
