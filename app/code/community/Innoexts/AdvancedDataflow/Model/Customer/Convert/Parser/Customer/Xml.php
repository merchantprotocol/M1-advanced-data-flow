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
 * Customer xml parser
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Customer_Convert_Parser_Customer_Xml extends Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml
{
    /**
     * Entity tag
     * 
     * @var string
     */
    protected $_entityTag = 'customer';
    /**
     * Entity plural tag
     * 
     * @var string
     */
    protected $_entityPluralTag = 'customers';
    /**
     * Get addresses rows indexes
     * 
     * @param array $row
     * @return array
     */
    protected function getAddressesRowsIndexes($row)
    {
        $indexes = array();
        foreach ($row as $key => $value) {
            $prefix = 'address_';
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $parts = explode('_', $key);
                if (count($parts) > 1) {
                    $index = $parts[1];
                    if (((int) $index == $index) && !in_array($index, $indexes)) {
                        array_push($indexes, $index);
                    }
                }
            }
        }
        return $indexes;
    }
    /**
     * Check if address row empty
     * 
     * @param array $row
     * @return boolean
     */
    protected function isAddressRowEmpty($row)
    {
        $keys = array('firstname');
        $isEmpty = false;
        foreach ($keys as $key) {
            if (empty($row[$key])) { $isEmpty = true; break; }
        }
        return $isEmpty;
    }
    /**
     * Read data collection and write to temporary file
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml
     */
    public function unparse()
    {
        $batchExport = $this->getBatchExportModel()->setBatchId($this->getBatchModel()->getId());
        $batchExportIds = $batchExport->getIdCollection();
        if (!$batchExportIds) return $this;
        $io = $this->getBatchModel()->getIoAdapter();
        $io->open();
        $io->write($this->getHeaderXml());
        foreach ($batchExportIds as $batchExportId) {
            $batchExport->load($batchExportId);
            $row = $batchExport->getBatchData();
            $billingRow = $this->extractRowFields($row, 'billing');
            if (count($billingRow)) {
                $row = $this->unsetRowFields($row, 'billing');
                $row['billing'] = $billingRow;
            }
            $shippingRow = $this->extractRowFields($row, 'shipping');
            if (count($shippingRow)) {
                $row = $this->unsetRowFields($row, 'shipping');
                $row['shipping'] = $shippingRow;
            }
            $indexes = $this->getAddressesRowsIndexes($row);
            $addressesRows = array();
            foreach ($indexes as $index) {
                $prefix = 'address_'.$index;
                $addressRow = $this->extractRowFields($row, $prefix);
                if (count($addressRow)) {
                    $row = $this->unsetRowFields($row, $prefix);
                    if (!$this->isAddressRowEmpty($addressRow)) {
                        array_push($addressesRows, array('address' => $addressRow));
                    }
                }
            }
            if (count($addressesRows)) $row['addresses'] = $addressesRows;
            $io->write($this->arrayToXml(array($this->getEntityTag() => $row, ), 1));
        }
        $io->write($this->getFooterXml());
        $io->close();
        return $this;
    }
    /**
     * Parse node
     * 
     * @param DOMNode $node
     * @param string $prefix
     * @return array
     */
    protected function parseNode($node, $prefix = '')
    {
        $row = array();
        if ($node->hasChildNodes()) {
    	    foreach ($node->childNodes as $attributeNode) {
                if ($attributeNode->nodeType == 1) {
                    $children = array('billing', 'shipping');
                    $nodeName = strtolower($attributeNode->nodeName);
                    if (in_array($nodeName, $children)) {
                        $childRow = parent::parseNode($attributeNode, $nodeName.'_');
                        if (count($childRow)) $row = array_merge($row, $childRow);
                    } else if (in_array($nodeName, array('addresses'))) {
                        if ($attributeNode->hasChildNodes()) {
                            $index = 1;
                            foreach ($attributeNode->childNodes as $childAttributeNode) {
                                if ($childAttributeNode->nodeType == 1) {
                                    $childRow = parent::parseNode($childAttributeNode, 'address_'.strval($index).'_');
                                    if (count($childRow)) $row = array_merge($row, $childRow);
                                    $index++;
                                }
                            }
                        }
                    } else {
                        $row[$attributeNode->nodeName] = (string) $attributeNode->nodeValue;
                    }
                }
            }
        }
        return $row;
    }
}
