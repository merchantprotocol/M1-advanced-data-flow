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
 * Convert xml parser
 * 
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @author     Mage Plugins <mageplugins@gmail.com>
 */
class MP_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml extends Mage_Dataflow_Model_Convert_Parser_Abstract 
{
    /**
     * New line delimiter
     * 
     * @var string
     */
    protected $_nl = "\r\n";
    /**
     * Tab
     * 
     * @var string
     */
    protected $_tab = "\t";
    /**
     * Entity tag
     * 
     * @var string
     */
    protected $_entityTag = 'entity';
    /**
     * Entity plural tag
     * 
     * @var string
     */
    protected $_entityPluralTag = 'entities';
    /**
     * Dom document object
     *
     * @var DOMDocument
     */
    protected $_domDocument;
    /**
     * Get helper
     * 
     * @return MP_AdvancedDataflow_Helper_Data
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
     * @return MP_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
     */
    protected function setDomDocument($domDocument)
    {
        $this->_domDocument = $domDocument;
    }
    /**
     * Load DOM document
     * 
     * @param string $file
     * @return MP_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Xslt
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
     * Get new line characters
     * 
     * @param integer $count
     * @return string
     */
    protected function getNL($count = 1)
    {
        return  ($count > 0) ? (($count > 1) ? str_repeat($this->_nl, $count) : $this->_nl) : '';
    }
    /**
     * Get tab characters
     * 
     * @param integer $count
     * @return string
     */
    protected function getTab($count = 1)
    {
        return ($count > 0) ? (($count > 1) ? str_repeat($this->_tab, $count) : $this->_tab) : '';
    }
    /**
     * Get entity tag
     * 
     * @return string
     */
    protected function getEntityTag()
    {
        return $this->_entityTag;
    }
    /**
     * Entity plural tag
     * 
     * @return string
     */
    protected function getEntityPluralTag()
    {
        return $this->_entityPluralTag;
    }
    /**
     * Get string helper
     * 
     * @return Mage_Core_Helper_String
     */
    protected function getStringHelper()
    {
        return Mage::helper('core/string');
    }
    /**
     * Check if string has reserved xml characters
     * 
     * @param string $string
     * @return boolean
     */
    protected function isCDataString($string)
    {
        return ((strpos($string, '<') !== false) || (strpos($string, '&') !== false)) ? true : false;
    }
    /**
     * Convert array to xml
     * 
     * @param array $array
     * @param integer $indent
     * @return string
     */
    protected function arrayToXml($array, $indent = 0)
    {
        $string = $this->getStringHelper();
        $xml = '';
        if (is_array($array)) {
            if (!isset($array[0])) {
                foreach ($array as $key => $value) {
                    $key = $string->cleanString(trim($key));
                    $xml .= $this->getTab($indent).'<'.$key.'>';
                    if (is_array($value)) {
                        $xml .= $this->getNL().$this->arrayToXml($value, $indent + 1).$this->getTab($indent);
                    } else {
                        if (is_string($value)) {
                            $value = $string->cleanString(trim($value));
                            if ($this->isCDataString($value)) $xml .= '<![CDATA['.$value.']]>';
                            else $xml .= $value;
                        } elseif (is_numeric($value)) {
                            $xml .= strval($value);
                        } elseif (is_bool($value)) {
                            $xml .= ($value) ? '1' : '0';
                        }
                    }
                    $xml .= '</'.$key.'>'.$this->getNL();
                }
            } else {
                foreach ($array as $index => $value) {
                    if (is_array($value)) $xml .= $this->arrayToXml($value, $indent);
                }
            }
        }
        return $xml;
    }
    /**
     * Get header xml
     * 
     * @return string
     */
    protected function getHeaderXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'.$this->getNL().'<'.$this->getEntityPluralTag().'>'.$this->getNL();
    }
    /**
     * Get footer xml
     * 
     * @return string
     */
    protected function getFooterXml()
    {
        return '</'.$this->getEntityPluralTag().'>';
    }
    /**
     * Get XPath expression
     * 
     * @return string
     */
    protected function getXPath() {
        return $this->getVar('xpath', '//'.$this->getEntityPluralTag().'/'.$this->getEntityTag());
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
                    $row[$prefix.$attributeNode->nodeName] = (string) $attributeNode->nodeValue;
                }
            }
        }
        return $row;
    }
    /**
     * Parse xml
     * 
     * @return MP_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml
     */
    public function parse()
    {
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
        $helper = $this->getHelper();
        $adapterName = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');
        if (!$adapterName || !$adapterMethod) {
            $message = $helper->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }
        try {
            $adapter = Mage::getModel($adapterName);
        } catch (Exception $e) {
            $message = $helper->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }
        if (!is_callable(array($adapter, $adapterMethod))) {
            $message = $helper->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }
        $batchModel = $this->getBatchModel();
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();
        if (Mage::app()->getRequest()->getParam('files')) {
            $file = Mage::app()->getConfig()->getTempVarDir().'/import/'.urldecode(Mage::app()->getRequest()->getParam('files'));
            $this->_copy($file);
        }
        $batchIoAdapter->open(false);
        $countRows = 0;
        $dataFile = $batchIoAdapter->getFile(true);
        $this->loadDomDocument($dataFile);
        $xPath = new DOMXPath($this->getDomDocument());
        $nodes = $xPath->query($this->getXPath());
        foreach ($nodes as $node) {
            $row = $this->parseNode($node);
            $countRows++;
            $batchImportModel = $this->getBatchImportModel()->setId(null)->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)->setStatus(1)->save();
    	}
        $this->addException($helper->__('Found %d rows.', $countRows));
        $this->addException($helper->__('Starting %s :: %s', $adapterName, $adapterMethod));
        $batchModel->setParams($this->getVars())->setAdapter($adapterName)->save();
        return $this;
    }
    /**
     * Read data collection and write to temporary file
     * 
     * @return MP_AdvancedDataflow_Model_Dataflow_Convert_Parser_Xml
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
            $io->write($this->arrayToXml(array($this->getEntityTag() => $row, ), 1));
        }
        $io->write($this->getFooterXml());
        $io->close();
        return $this;
    }
    /**
     * Get array elements that start with prefix
     * 
     * @param array $origionalRow
     * @param string $prefix
     * @param array $exclude
     * @return array
     */
    protected function extractRowFields($origionalRow, $prefix, $exclude = array())
    {
        $row = array();
        if (is_array($origionalRow) && count($origionalRow)) {
            foreach ($origionalRow as $key => $value) {
                if (!in_array($key, $exclude) && (substr($key, 0, strlen($prefix) + 1) == $prefix.'_')) {
                    $row[substr($key, strlen($prefix) + 1)] = $origionalRow[$key];
                }
            }
        }
        return $row;
    }
    /**
     * Unset array elements that start with prefix
     * 
     * @param array $origionalRow
     * @param string $prefix
     * @param array $exclude
     * @return array
     */
    protected function unsetRowFields($origionalRow, $prefix, $exclude = array())
    {
        if (is_array($origionalRow) && count($origionalRow)) {
            foreach ($origionalRow as $key => $value) {
                if (!in_array($key, $exclude) && (substr($key, 0, strlen($prefix) + 1) == $prefix.'_')) {
                    unset($origionalRow[$key]);
                }
            }
        }
        return $origionalRow;
    }
}
