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
 * Category adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Catalog_Convert_Adapter_Category extends Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Entity
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setVar('entity_type', 'catalog/category');
    }
    /**
     * Load collection identifiers
     * 
     * @return Innoexts_AdvancedDataflow_Model_Catalog_Convert_Adapter_Category
     */
    public function load()
    {
        return parent::load();
    }
    /**
     * Save row
     * 
     * @param array $row
     * @return Innoexts_AdvancedDataflow_Model_Catalog_Convert_Adapter_Category
     */
    public function saveRow($row)
    {
        return parent::saveRow($row);
    }
    
    /**
     * Get filters vars
     * 
     * @return array
     */
    /*
    protected function getFiltersVars()
    {
        $vars = $this->getVars();
        $filters = array();
        foreach ($vars as $key => $value) {
            if (substr($key, 0, 6) === 'filter') { $keys = explode('/', $key, 2); $filters[$keys[1]] = $value; }
        }
        return $filters;
    }
    */
    /**
     * Get field filters
     * 
     * @return array
     */
    protected function getFieldFilters()
    {
        $filters = array(
            'name' => 'startsWith', 
        );
        return $filters;
    }
    /**
     * Prepare filters variables
     * 
     * @return Innoexts_AdvancedDataflow_Model_Catalog_Convert_Adapter_Category
     */
    protected function prepareFiltersVars()
    {
        /*
        $datetimeAttributes = array('created_at', 'updated_at');
        foreach ($datetimeAttributes as $attribute) {
            $fromKey = 'filter/'.$attribute.'/from';
            $toKey = 'filter/'.$attribute.'/to';
            if ($var = $this->getVar($fromKey)) $this->setVar($fromKey, $var.' 00:00:00');
            if ($var = $this->getVar($toKey)) $this->setVar($toKey, $var.' 23:59:59');
        }
        return $this;
        */
        return $this;
    }
}
