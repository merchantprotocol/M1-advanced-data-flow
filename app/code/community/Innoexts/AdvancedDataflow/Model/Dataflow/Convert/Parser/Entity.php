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
 * Entity parser
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Entity extends Mage_Dataflow_Model_Convert_Parser_Abstract
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
     * Unparse entity
     * 
     * @return array
     */
    protected function unparseEntity()
    {
        return $this->getEntity()->convertEntityToArray();
    }
    /**
     * Unparse
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Entity
     */
    public function unparse()
    {
        $helper = $this->getHelper();
        $entity = $this->getEntity();
        # echo $entity->getHtml();
        $entityIds = $this->getData();
        foreach ($entityIds as $i => $entityId) {
            $entity->reloadModel($entityId);
            $position = $helper->__('Line %d, ID: %s', $i, $entity->getId());
            $this->setPosition($position);
            $row = $this->unparseEntity();
            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
        }
        return $this;
    }
    /**
     * Parse
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Entity
     */
    public function parse()
    {
        return $this;
    }
}