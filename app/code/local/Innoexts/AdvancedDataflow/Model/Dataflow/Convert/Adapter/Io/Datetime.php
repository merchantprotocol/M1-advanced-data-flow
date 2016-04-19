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
 * Convert IO datetime adapter
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Convert_Adapter_Io_Datetime extends Mage_Dataflow_Model_Convert_Adapter_Io
{
    /**
     * Get file name format
     * 
     * @return string
     */
    protected function getFilenameFormat()
    {
        return $this->getVar('filename_format');
    }
    /**
     * Get file name
     * 
     * @return string
     */
    protected function getFilename()
    {
        return ($this->getFilenameFormat()) ? 
            strftime($this->getFilenameFormat(), strtotime($this->getVar('filename'))) : $this->getVar('filename');
    }
    /**
     * @return Varien_Io_Abstract
     */
    public function getResource($forWrite = false)
    {
        $this->setVar('filename', $this->getFilename());
        $this->setVar('filename_format', null);
        if (isset($this->_vars['filename_format'])) unset($this->_vars['filename_format']);
        return parent::getResource($forWrite);
    }
}