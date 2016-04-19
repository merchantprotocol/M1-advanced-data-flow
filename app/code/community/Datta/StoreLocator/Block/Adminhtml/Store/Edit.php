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
 * @created     Dattatray Yadav  2nd Dec,2013 2:20pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Manage store location edit 
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Datta_StoreLocator_Block_Adminhtml_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
      * initlization of class
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'datta_storelocator'; 
        $this->_controller = 'adminhtml_store';         
        parent::__construct();                          
        $this->_updateButton('save', 'label', Mage::helper('datta_storelocator')->__('Save Store'));
        $this->_updateButton('delete', 'label', Mage::helper('datta_storelocator')->__('Delete Store'));
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('datta_storelocator')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $img="";
        $apiKey = Mage::getStoreConfig('storelocator/general/apikey');
        $apiUrl = Mage::getStoreConfig('storelocator/general/apiurl');  
        $apiSensor = Mage::getStoreConfig('storelocator/general/apisensor');
        $sensor = ($apiSensor == 0) ? 'false' : 'true';        
        $marker = "var marker = new google.maps.Marker({position: latLng, map: map ,draggable: true});
         google.maps.event.addListener(marker, 'dragend', function(marker){
             var latLng = marker.latLng;
             document.getElementById('lat').value = latLng.lat();
             document.getElementById('long').value = latLng.lng();          
         });                                                                   
        ";
        if(!is_null(Mage::getStoreConfig('storelocator/general/mapicon')) && Mage::getStoreConfig('storelocator/general/mapicon') != '') {
            $img = "var imgMarker =  new google.maps.MarkerImage('".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'storelocator/markers/'.Mage::getStoreConfig('storelocator/general/mapicon')."');";
            $marker = "var marker = new google.maps.Marker({position: latLng, icon: imgMarker,map: map });";
        }                                                                                               
        $this->_formScripts[] = "   
            function loadScript(){
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = '".$apiUrl."&callback=getLatLng&sensor=".$sensor."&key=".$apiKey."';
                document.head.appendChild(script);
                var div = document.createElement('div');
                div.id='map_canvas';
                document.getElementsByClassName('hor-scroll')[0].appendChild(div);
                var img = document.createElement('img');
                document.getElementsByClassName('form-list')[0].style.float='left';
                document.getElementById('map_canvas').style.height='500px';
                document.getElementById('map_canvas').style.width='500px';
                document.getElementById('map_canvas').style.float='left';
                document.getElementById('map_canvas').style.marginLeft='30px';
                document.getElementById('map_canvas').style.marginTop='6px';
            }    
            window.onload = loadScript;

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            function capitalize(str) {
                var pieces = str.split(' ');
                for ( var i = 0; i < pieces.length; i++ )
                {
                    var j = pieces[i].charAt(0).toUpperCase();
                    pieces[i] = j + pieces[i].substr(1);
                }
                return pieces.join(' ');
            }              
            function getLatLng(){
                ".$img."
                var imgStore = storeImage = ''; 
                var geocoder = new google.maps.Geocoder();
                var address = document.getElementById('address').value;
                var city = document.getElementById('city').value;
                var country = document.getElementById('country_id').options[document.getElementById('country_id').selectedIndex].text;
                if(document.getElementById('image_image')){
                    storeImage = document.getElementById('image_image').src;
                    imgStore = '<div><img src='+storeImage+' alt='+document.getElementById('name').value+' style=\'float:left;width:150px;\'/></div>';
                }
                if(document.getElementById('marker_image')){
                    storeMarker = document.getElementById('marker_image').src;
                }                                                                                  
                if(address != '' && city != ''){
                    var addressComplete = address + ', ' + city;
                    if(country != '') addressComplete = addressComplete + ' ' + country;
                    geocoder.geocode( { 'address': addressComplete}, function(results, status) {
                      if (status == google.maps.GeocoderStatus.OK) {
                        document.getElementById('lat').value = results[0].geometry.location.lat();
                        document.getElementById('long').value = results[0].geometry.location.lng();
                        document.getElementById('address').value = capitalize(document.getElementById('address').value);
                        document.getElementById('city').value = capitalize(document.getElementById('city').value);
                        var latLng =  new google.maps.LatLng(document.getElementById('lat').value , document.getElementById('long').value);
                        var mapOption = {zoom: 18, center: latLng, mapTypeId: google.maps.MapTypeId.ROADMAP, disableDefaultUI : true };
                        map = new google.maps.Map(document.getElementById('map_canvas'), mapOption);
                        if(document.getElementById('marker_image')){
                            storeMarker = document.getElementById('marker_image').src;
                            var marker = new google.maps.Marker({position: latLng, icon: storeMarker,map: map });
                        }else{
                        ".$marker."
                        }
                        var infoWindow = new google.maps.InfoWindow();
                        google.maps.event.addListener(marker, 'click', (function(marker) {
                            return function() {
                                var content = 	imgStore+'<div style=\'float:left;margin-top: 10px; overflow: auto; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); border-top-left-radius: 10px; border-top-right-radius: 10px;  border-bottom-right-radius: 10px; border-bottom-left-radius: 10px;\' ><h3>' + document.getElementById('name').value + '</h3>'
                                + document.getElementById('address').value + '<br>'
                                + document.getElementById('zipcode').value+' '+document.getElementById('city').value +' <br>'
                                + document.getElementById('country_id').options[document.getElementById('country_id').selectedIndex].text+'<br>'
                                + document.getElementById('phone').value + '<br>'
                                + document.getElementById('fax').value + '<br>'
                                +'</div>';
                                infoWindow.setContent(content);
                                infoWindow.open(map,marker);
                            }
                        })(marker));
                      }
                    });
                }
            }    
        ";
    }            
    /**
     * Get Header text
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('datta_storelocator')->getId()) {
            return $this->__('Edit School');
        }
        else {
            return $this->__('New School');
        }
    }
}