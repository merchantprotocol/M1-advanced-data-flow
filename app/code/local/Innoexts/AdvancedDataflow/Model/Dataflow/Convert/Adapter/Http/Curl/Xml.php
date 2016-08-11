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
 * Convert HTTP XML Curl adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl_Xml 
    extends Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl
{
    /**
     * Dom document object
     *
     * @var DOMDocument
     */
    protected $_domDocument;
    /**
     * Get entity XPath
     * 
     * @return string
     */
    protected function getEntityXPath()
    {
        return $this->getVar('entity_xpath', '//data/xpath');
    }
    /**
     * Check if entities should be saved separately
     * 
     * @return boolean
     */
    protected function isMultipleSave()
    {
        $isMultipleSave = $this->getVar('is_multiple_save', 0);
        return ($isMultipleSave) ? true : false;
    }
    /**
     * Get DOM document
     * 
     * @return DOMDocument
     */
    protected function getDomDocument()
    {
        if (!$this->_domDocument) {
            try {
                $this->_domDocument = new DOMDocument();
            } catch (Exception $e) {
                $helper = $this->getHelper();
                $message = $helper->__('Unable to load DOM extension: "%s".', $e->getMessage());
                Mage::throwException($message);
            }
        }
        return $this->_domDocument;
    }
    /**
     * Set DOM document
     * 
     * @param $domDocument DOMDocument
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
     */
    protected function setDomDocument($domDocument)
    {
        $this->_domDocument = $domDocument;
    }
    /**
     * Load DOM document
     * 
     * @param string $file
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
     */
    protected function loadDomDocument($file)
    {
        $domDocument = $this->getDomDocument();
        libxml_use_internal_errors(true);
        $domDocument->load($file);
        libxml_use_internal_errors(false);
        return $this;
    }
    /**
     * Save result
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl
     */
    public function save()
    {
        if ($this->isMultipleSave()) {
            $helper = $this->getHelper();
            if (!$this->getResource()) return $this;
            $batchModel = Mage::getSingleton('dataflow/batch');
            $io = $batchModel->getIoAdapter();
            try {
                $dataFile = $io->getFile(true);
                $this->loadDomDocument($dataFile);
                $domDocument = $this->getDomDocument();
                $xPath = new DOMXPath($domDocument);
                $nodes = $xPath->query($this->getEntityXPath());
                foreach ($nodes as $position => $node) {
                    $tmpDomDocument = clone $domDocument;
                    $tmpXPath = new DOMXPath($tmpDomDocument);
                    $tmpNodes = $tmpXPath->query($this->getEntityXPath());
                    foreach ($tmpNodes as $tmpPosition => $tmpNode) {
                        if ($position != $tmpPosition) {
                            $tmpNode->parentNode->removeChild($tmpNode);
                        }
                    }
                    $request = $tmpDomDocument->saveHTML();
                    $response = $this->request($request);
                    unset($tmpXPath);
                    unset($tmpDomDocument);
                }
                unset($xPath);
                unset($domDocument);
            } catch (Exception $e) {
                $helper = $this->getHelper();
                $message = $helper->__('Unable to save.', $e->getMessage());
                Mage::throwException($message);
            }
            return $this;
        } else return parent::save();
    }
}
