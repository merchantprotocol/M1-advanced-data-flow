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
 * Convert HTTP Curl adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
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
     * Get resource object
     * 
     * @return Varien_Http_Adapter_Curl
     */
    public function getResource() {
        if (!$this->_resource) {
            $this->_resource = new Varien_Http_Adapter_Curl();
        }
        return $this->_resource;
    }
    /**
     * Get method
     * 
     * @return string
     */
    protected function getMethod()
    {
        $method = $this->getVar('method', 'get');
        return (strtoupper($method) == 'POST') ? Zend_Http_Client::POST : Zend_Http_Client::GET;
    }
    /**
     * Get URL
     * 
     * @return string
     */
    protected function getURL()
    {
        return $this->getVar('url');
    }
    /**
     * Get version
     * 
     * @return string
     */
    protected function getVersion()
    {
        return $this->getVar('version', '1.0');
    }
    /**
     * Get headers
     * 
     * @return 
     */
    protected function getHeaders()
    {
        $headers = array();
        $headerPrefix = 'header/';
        foreach ($this->getVars() as $key => $value) {
            if (substr($key, 0, strlen($headerPrefix)) == $headerPrefix) {
                $headers[substr($key, strlen($headerPrefix))] = $value;
            }
        }
        return $headers;
    }
    /**
     * Get request
     * 
     * @return string
     */
    protected function getRequest()
    {
        return $this->getVar('request');
    }
    /**
     * Make HTTP request
     * 
     * @param string $request
     * @return string
     */
    protected function request($request)
    {
        $helper = $this->getHelper();
        if (!$this->getResource()) return null;
        $resource = $this->getResource();
        $method = $this->getMethod();
        $url = $this->getURL();
        if (!Zend_Uri::check($url)) {
            $message = $helper->__('Expecting a valid URL parameter.');
            Mage::throwException($message);
        }
        $version = $this->getVersion();
        $headers = $this->getHeaders();
        $resource->write($method, $url, $version, $headers, $request);
        $response = $resource->read();
        if ($resource->getErrno()) {
            $message = $helper->__($resource->getError());
            Mage::throwException($message);
        }
        if (Zend_Http_Response::extractCode($response) != 200) {
            $helper = $this->getHelper();
            $message = $helper->__('Wrong responce code received.');
            Mage::throwException($message);
        }
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = (isset($response[1])) ? trim($response[1]) : null;
        return $response;
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
        die($output);
    }
    /**
     * Load
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl
     */
    public function load()
    {
        $helper = $this->getHelper();
        $batchModel = Mage::getSingleton('dataflow/batch');
        $io = $batchModel->getIoAdapter();
        try {
            $destFile = $io->getFile(true);
            $response = $this->request($this->getRequest());
            if ($response) {
                $io->open(true);
                $io->write($response);
                $io->close();
                $this->setData($response);
                $message = $helper->__('Loaded successfully: "%s".', $this->getURL());
                $this->addException($message);
            } else {
                $message = $helper->__('Could not load URL: "%s".', $this->getURL());
                Mage::throwException($message);
            }
            if ($this->isDebug()) $this->debug($response);
        } catch (Exception $e) {
            $message = $helper->__('Unable to load.', $e->getMessage());
            Mage::throwException($message);
        }
        return $this;
    }
    /**
     * Save result
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Http_Curl
     */
    public function save()
    {
        $helper = $this->getHelper();
        if (!$this->getResource()) return $this;
        $batchModel = Mage::getSingleton('dataflow/batch');
        $io = $batchModel->getIoAdapter();
        try {
            $request = '';
            $io->open(false);
            do { $buffer = $io->read(); $request .= $buffer; } while ($buffer !== false);
            $io->close();
            $response = $this->request($request);
            if ($this->isDebug()) $this->debug($response);
        } catch (Exception $e) {
            $helper = $this->getHelper();
            $message = $helper->__('Unable to save.', $e->getMessage());
            Mage::throwException($message);
        }
        return $this;
    }
}