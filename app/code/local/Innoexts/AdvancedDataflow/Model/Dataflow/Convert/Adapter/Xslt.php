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
 * Convert XSLT adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * Dom document object
     *
     * @var DOMDocument
     */
    protected $_domDocument;
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
     * Get resource object
     * 
     * @return XSLTProcessor
     */
    protected function _getResource()
    {
        $resource = null;
        try {
            $resource = new XSLTProcessor();
        } catch (Exception $e) {
            $helper = $this->getHelper();
            $message = $helper->__('Unable to load XSL extension: "%s".', $e->getMessage());
            Mage::throwException($message);
        }
        return $resource;
    }
    /**
     * Get XSLT path
     * 
     * @return string
     */
    protected function getXsltPath()
    {
        return $this->getVar('xslt_path', 'var/xslt');
    }
    /**
     * Get XSLT filename
     * 
     * @return string
     */
    protected function getXsltFilename()
    {
        return $this->getVar('xslt_filename');
    }
    /**
     * Get XSLT file
     * 
     * @return string
     */
    protected function getXsltFile()
    {
        return Mage::getBaseDir().DS.rtrim($this->getXsltPath(), '\\/').DS.$this->getXsltFilename();
    }
    /**
     * Get available php functions
     * 
     * @return array
     */
    protected function getAvailablePHPFucntions()
    {
        return null;
    }
    /**
     * Get output format
     * 
     */
    protected function getOutputFormat()
    {
        return strtolower($this->getVar('output_format', 'xml'));
    }
    /**
     * Check if XML output format enabled
     * 
     * @return boolean
     */
    protected function isXMLOutputFormat()
    {
        return ($this->getOutputFormat() == 'xml') ? true : false;
    }
    /**
     * Check if adapter in debug mode
     * 
     * @return boolean
     */
    protected function isDebug()
    {
        $debug = $this->getVar('debug', 0);
        return ($debug) ? true : false;
    }
    /**
     * Output debug info
     * 
     * @param string $output
     */
    protected function debug($output)
    {
        ob_end_clean();
        if (substr($output, 0, 5) == '<?xml') header('Content-Type: text/xml; charset=utf-8');
        die($output);
    }
    /**
     * Get resource
     * 
     * @return XSLTProcessor
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $resource = $this->_getResource();
            try {
                $this->loadDomDocument($this->getXsltFile());
                $resource->registerPHPFunctions();
                $resource->importStyleSheet($this->getDomDocument());
                $this->_resource = $resource;
            } catch (Exception $e) {
                $helper = $this->getHelper();
                $message = $helper->__('Unable to import XSLT style sheet: "%s".', $e->getMessage());
                Mage::throwException($message);
            }
        }
        return $this->_resource;
    }
    /**
     * Load
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
     */
    public function load()
    {
        return $this;
    }
    /**
     * Save result
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
     */
    public function save()
    {
        $helper = $this->getHelper();
        if (!$this->getResource()) return $this;
        $batchModel = Mage::getSingleton('dataflow/batch');
        $io = $batchModel->getIoAdapter();
        $dataFile = $io->getFile(true);
        try {
            $resource = $this->getResource();
            $this->loadDomDocument($dataFile);
            foreach ($this->getVars() as $key => $value) {
                $resource->setParameter('', $key, strval($value));
            }
            $domDocument = $resource->transformToDoc($this->getDomDocument());
            $domDocument->formatOutput = true;
            if ($this->isXMLOutputFormat()) {
                $output = $domDocument->saveXML();
            } else {
                $output = $domDocument->saveHTML();
            }
            $io->open(true);
            $io->write($output);
            $io->close();
            $this->setData($output);
            if ($this->isDebug()) $this->debug($output);
        } catch (Exception $e) {
            $helper = $this->getHelper();
            $message = $helper->__('Unable to transform XML file: "%s".', $e->getMessage());
            Mage::throwException($message);
        }
        return $this;
    }
}
