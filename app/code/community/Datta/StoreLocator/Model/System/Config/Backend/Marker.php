<?php

 /**
 * Magento 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *  
 * 
 * @category    Datta
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  29th Nov,2013 6:20pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Backend model for uploading transactional emails custom logo image
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License 
 */
class Datta_StoreLocator_Model_System_Config_Backend_Marker extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR                = 'storelocator/markers';

    /**
     * Token for the root part of directory path for uploading
     */
    const UPLOAD_ROOT_TOKEN         = 'system/filesystem/media';

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize         = 2048;

    /**
     * Return path to directory for upload file
     *
     * @return string
     */
    protected function _getUploadDir()
    {
        $uploadDir  = $this->_appendScopeInfo(self::UPLOAD_DIR);
        $uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN);
        $uploadDir  = $uploadRoot . DS . $uploadDir;
        return $uploadDir;
    }

    /**
     * Makes a decision about whether to add info about the scope
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Save uploaded file before saving config value
     *
     * Save changes and delete file if "delete" option passed
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Email_Logo
     */
    protected function _beforeSave()
    {
        $value       = $this->getValue();
        $deleteFlag  = (is_array($value) && !empty($value['delete']));
        $fileTmpName = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];

        if ($this->getOldValue() && ($fileTmpName || $deleteFlag)) {
            $io = new Varien_Io_File();
            $io->rm($this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN) . DS . self::UPLOAD_DIR . DS . $this->getOldValue());
        }
        return parent::_beforeSave();
    }
}
