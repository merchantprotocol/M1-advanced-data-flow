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
 * Customerattribute Model
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Model_Customeraddressattribute extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customerattribute/customeraddressattribute');
    }
    public function addAddressAttribute($data)
    {           
        $setup = Mage::getModel('customer/entity_setup', 'core_setup');
        $isMulti = $data['frontend_input']== 'multiselect';
        $setup->addAttribute('customer_address',$data['attribute_code'],array(
            'type'      => $isMulti ? 'varchar(255)' : 'int',
            'input'     =>$data['frontend_input'],
            'label'     =>$data['frontend_label'],
            'visible'   => 1,
            'required'  =>$data['is_required'], 
            'sort_order'=>$data['sort_order'],
        ));       
       $attributeId = Mage::getSingleton('eav/config')->getAttribute('customer_address', $data['attribute_code'])
                                                       ->getAttributeId(); 
       $store = implode(",",$data['store_view']); 
       $model = Mage::getModel('customerattribute/customeraddressattribute');
       $data_setup = array(
            'attribute_id' => $attributeId,
            'status'       => $data['status'],
            'show_on_acount_address' => $data['display_on_frontend'][1],
            'show_on_checkout'  => $data['display_on_frontend'][0],
            'admin_create_order'=> $data['show_on_backend'][0],
            'admin_edit_address'=> $data['show_on_backend'][1],
            'store_id'          =>$store,
       );
       $model->setData($data_setup)->save();
          
    }
}