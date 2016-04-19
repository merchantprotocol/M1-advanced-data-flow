<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customerattribute Helper
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Helper_Image extends Mage_Core_Helper_Abstract
{
//create folder
	public function createImageFolder($brandName) {
        $brandPath = Mage::getBaseDir('media') . DS . 'customer';
        $brandImagePath = Mage::getBaseDir('media') . DS . 'customer' . DS .substr($brandName,0,1);
		if(substr($brandName,1,1)!='.')
        $brandImagePathCache = Mage::getBaseDir('media') . DS . 'customer' . DS .substr($brandName,0,1).DS.substr($brandName,1,1);
		else 
		$brandImagePathCache=$brandImagePath;
        if (!is_dir($brandPath)) {
            try {

                chmod(Mage::getBaseDir('media'), 0777);

                mkdir($brandPath);

                chmod($brandPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandImagePath)) {
            try {
                chmod($brandPath, 0777);

                mkdir($brandImagePath);

                chmod($brandImagePath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandImagePathCache)) {
            try {

                mkdir($brandImagePathCache);

                chmod($brandImagePathCache, 0777);
				return $brandImagePathCache;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }
		return $brandImagePathCache;
    }
	public function getUrlkey($imageName)
	{
		$brandImagePath = '/'.substr($imageName,0,1).'/'.$imageName;
		if(substr($imageName,1,1)!='.')
        $brandImagePathCache ='/'.substr($imageName,0,1).'/'.substr($imageName,1,1).'/'.$imageName;
		else 
		$brandImagePathCache=$brandImagePath;
		return $brandImagePathCache;
	}
	
}