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
 * @created     Dattatray Yadav  2nd Dec,2013 1:45pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Retrieve the default sotre and google api url
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License   
 */
class Datta_StoreLocator_Block_Store extends Mage_Core_Block_Template
{
    public function getDefaultMarker(){
        $defaultMarker = '';
        if(!is_null(Mage::getStoreConfig('storelocator/general/mapicon')) && Mage::getStoreConfig('storelocator/general/mapicon') != ''){
            $defaultMarker = 'storelocator/markers/'.Mage::getStoreConfig('storelocator/general/mapicon');
        }
        return $defaultMarker;
    }
    public function getStores(){
        $stores = Mage::getModel('datta_storelocator/store')->getCollection()
            ->addFieldToFilter('status',1)
            ->addStoreFilter($this->getCurrentStore())
            ->addFieldToSelect(
                array(
                    'entity_id',
                    'name',
                    'address',
                    'zipcode',
                    'city',
                    'country_id',
                    'phone',
                    'fax',
                    'description',
                    'store_url',
                    'image',
                    'marker',
                    'lat',
                    'long'));

        $storesCollection = new Varien_Data_Collection();
        foreach($stores as $store){
           if(!is_null($store->getCountryId())){
               $store->setCountryId($this->getCountryByCode($store->getCountryId()));
           }else{
               $store->setCountryId($this->__('NC'));
           }  
            if(!is_null($store->getImage()) || $store->getImage() != ''){
                $imgUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$store->getImage();
            }elseif (!is_null(Mage::getStoreConfig('storelocator/general/defaultimage')) && Mage::getStoreConfig('storelocator/general/defaultimage') != ''){
                $imgUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'storelocator/images/'.Mage::getStoreConfig('storelocator/general/defaultimage');
            }else{
                $imgUrl = $this->getLogoSrc();
            }
            $store->setImage($imgUrl);
           $storesCollection->addItem($store);
        }                 
        return $storesCollection;
    } 
    public function getGoogleApiUrl(){
        $apiUrl = Mage::getStoreConfig('storelocator/general/apiurl');
        if(is_null($apiUrl))
            $apiUrl = "http://maps.googleapis.com/maps/api/js?v=3";
        $apiKey = "&key=".Mage::getStoreConfig('storelocator/general/apikey');
        $apiSensor = Mage::getStoreConfig('storelocator/general/apisensor');
        $sensor = ($apiSensor == 0) ? 'false' : 'true';
        $urlGoogleApi = $apiUrl."&sensor=".$sensor.$apiKey."&callback=initialize&libraries=places";        
        return $urlGoogleApi;
    }        
    /**
     * retrieve current store     
     * return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore()->getId();
    }
    public function getCountryByCode($code){
        return Mage::getModel('directory/country')->loadByCode($code)->getName();
    }
    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
        }
        return $this->getSkinUrl($this->_data['logo_src']);
    }
}