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
