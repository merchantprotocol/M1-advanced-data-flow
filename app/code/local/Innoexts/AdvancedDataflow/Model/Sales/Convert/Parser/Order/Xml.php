<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Order xml parser
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Sales_Convert_Parser_Order_Xml extends Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml
{
    /**
     * Entity tag
     * 
     * @var string
     */
    protected $_entityTag = 'order';
    /**
     * Entity plural tag
     * 
     * @var string
     */
    protected $_entityPluralTag = 'orders';
    /**
     * Get items rows indexes
     * 
     * @param array $row
     * @return array
     */
    protected function getItemsRowsIndexes($row)
    {
        $indexes = array();
        foreach ($row as $key => $value) {
            $prefix = 'item_';
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
     * Check if item row empty
     * 
     * @param array $row
     * @return boolean
     */
    protected function isItemRowEmpty($row)
    {
        $keys = array('sku');
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
            $shippingAddressRow = $this->extractRowFields($row, 'shipping_address');
            if (count($shippingAddressRow)) {
                $row = $this->unsetRowFields($row, 'shipping_address');
                $row['shipping_address'] = $shippingAddressRow;
            }
            $billingAddressRow = $this->extractRowFields($row, 'billing_address');
            if (count($billingAddressRow)) {
                $row = $this->unsetRowFields($row, 'billing_address');
                $row['billing_address'] = $billingAddressRow;
            }
            $indexes = $this->getItemsRowsIndexes($row);
            $itemsRows = array();
            foreach ($indexes as $index) {
                $prefix = 'item_'.$index;
                $itemRow = $this->extractRowFields($row, $prefix);
                if (count($itemRow)) {
                    $row = $this->unsetRowFields($row, $prefix);
                    if (!$this->isItemRowEmpty($itemRow)) {
                        array_push($itemsRows, array('item' => $itemRow));
                    }
                }
            }
            if (count($itemsRows)) {
                $row['items'] = $itemsRows;
            }
            $paymentExcludeFields = array('payment_authorization_amount', 'payment_authorization_expiration');
            $paymentRow = $this->extractRowFields($row, 'payment', $paymentExcludeFields);
            if (count($paymentRow)) {
                $row = $this->unsetRowFields($row, 'payment', $paymentExcludeFields);
                $row['payment'] = $paymentRow;
            }
            $io->write($this->arrayToXml(array($this->getEntityTag() => $row, ), 1));
            unset($row);
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
                    $children = array('billing_address', 'shipping_address', 'payment');
                    $nodeName = strtolower($attributeNode->nodeName);
                    if (in_array($nodeName, $children)) {
                        $childRow = parent::parseNode($attributeNode, $nodeName.'_');
                        if (count($childRow)) $row = array_merge($row, $childRow);
                    } else if (in_array($nodeName, array('items'))) {
                        if ($attributeNode->hasChildNodes()) {
                            $index = 1;
                            foreach ($attributeNode->childNodes as $childAttributeNode) {
                                if ($childAttributeNode->nodeType == 1) {
                                    $childRow = parent::parseNode($childAttributeNode, 'item_'.strval($index).'_');
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