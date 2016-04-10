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
     * Parse node
     * 
     * @param DOMNode $node
     * @param string $prefix
     * @return array
     */
    protected function parseNode($node, $prefix = '')
    {
        if ($node->hasChildNodes()) {
    	    foreach ($node->childNodes as $attributeNode) {
                if ($attributeNode->nodeType == 1) {
                    if (in_array(strtolower($attributeNode->nodeName), array('billing', 'shipping'))) {
                        $addressRow = parent::parseNode($attributeNode, strtolower($attributeNode->nodeName).'_');
                        if (count($addressRow)) $row = array_merge($row, $addressRow);
                    } else {
                        $row[$attributeNode->nodeName] = (string) $attributeNode->nodeValue;
                    }
                }
            }
        }
        return $row;
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
            $io->write($this->arrayToXml(array($this->getEntityTag() => $row, ), 1));
        }
        $io->write($this->getFooterXml());
        $io->close();
        return $this;
    }
}