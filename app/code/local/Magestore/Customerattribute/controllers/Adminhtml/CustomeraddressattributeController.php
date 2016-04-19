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
 * Customerattribute Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Adminhtml_CustomeraddressattributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Customerattribute_Adminhtml_CustomerattributeController
     */
    protected $_entityType;
    
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType('customer_address');
        }
        return $this->_entityType;
    }
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/customerattribute/customeraddressattribute')            
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Manage Customer Address Attribute'),
                Mage::helper('adminhtml')->__('Manage Customer Address Attribute')
            );
        return $this;
    }
    protected function _initAttribute()
    {
        $attribute = Mage::getModel('customer/attribute');                
        return $attribute;
    }
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {   
        $attributeId = $this->getRequest()->getParam('id'); 
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $entityTypeId     = $setup->getEntityTypeId('customer_address');
        $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customeraddressattribute');                
        $models = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId); 
        $models->getSelect()
               ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id');
        $models->addFieldToFilter('main_table.attribute_id',$attributeId);            
        $model=$models->getFirstItem();
        // register attribute object
        Mage::register('customeraddressattribute_data',$model);
        $this->loadLayout();
        $this->_setActiveMenu('customer/customerattribute/customeraddressattribute');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('customerattribute/adminhtml_customeraddressattribute_edit'))
            ->_addLeft($this->getLayout()->createBlock('customerattribute/adminhtml_customeraddressattribute_edit_tabs'));
        $this->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    /**
     * save item action
     */   
    public function saveAction()
    {
        $data = $this->getRequest()->getPost(); 
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject Mage_Customer_Model_Attribute */
            $attributeObject = $this->_initAttribute();
            $helper = Mage::helper('customerattribute');

            //filtering
            $data = $this->_filterPostData($data);                      
            $attributeId = $this->getRequest()->getParam('id');
            if ($attributeId) {
                $attributeObject->load($attributeId);                     
                $data['attribute_code']     = $attributeObject->getAttributeCode();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['frontend_input']     = $attributeObject->getFrontendInput();
                $data['is_system']          = $attributeObject->getIsSystem(); 
                
            }else {
                $data['attribute_code']= strtolower($data['attribute_code']);
                $string_code = $data['attribute_code'];
                while(strpos($string_code," ")) $string_code = str_replace(" ","",$string_code);  
                $data['attribute_code'] = $string_code;
                $data['backend_model']      = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model']       = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type']       = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined']    = 1;
                $data['is_system']          = 0;
                if($data['frontend_input']=='date')
                    $data['input_filter']='date';
                $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
                if ($defaultValueField) {
                    $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                    $data[$scopeKeyPrefix . 'default_value'] = $helper->stripTags(
                        $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField));
                }
                // add set and group info
                $data['attribute_set_id']   = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = Mage::getModel('eav/entity_attribute_set')
                    ->getDefaultGroupId($data['attribute_set_id']);
            }

            if($data['status']== 1){
                if($data['display_on_backend'] == 1) 
                     $data['used_in_forms'][] = 'adminhtml_customer_address';
                 if((int)in_array('0',$data['display_on_frontend'])== 1)
                     $data['used_in_forms'][] = 'customer_address_edit';
                if((int)in_array('1',$data['display_on_frontend'])== 1 
                        && $data['frontend_input'] != 'file' 
                        && $data['frontend_input'] != 'image' )
                     $data['used_in_forms'][] = 'customer_register_address';
                if(!$data['used_in_forms']) $data['used_in_forms']= array();
            }else $data['used_in_forms'][]= array();            
            $data['entity_type_id']     = $this->_getEntityType()->getId();
            $data['validate_rules']     = $helper->getAttributeValidateRules($data['frontend_input'], $data);          
            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }
            if($data['frontend_input']=='file'||$data['frontend_input']=='image')
                $data['is_required'] = 0;
            try {
                if ($attributeId) {                   
                    $attributeObject->load($attributeId)->setData($data);
                    $attributeObject->setId($attributeId)->save();
                 }else{ 
                     $attributeObject->setData($data)->save();                     
                 }
                Mage::dispatchEvent('customerattribute_customeraddressattribute_save', array(
                    'attribute' => $attributeObject
                ));
                $model = Mage::getModel('customerattribute/customeraddressattribute');
                $store = implode(",",$data['store_id']); 
                if($data['frontend_input']!= 'file' && $data['frontend_input'] != 'image' && (int)in_array('1',$data['display_on_frontend']) )
                    $show_on_checkout = 1;
                else $show_on_checkout = 0;
                $data_setup = array(
                    'attribute_id' => $attributeObject->getAttributeId(),
                    'status'       => $data['status'],
                    'show_on_acount_address' =>(int)in_array('0',$data['display_on_frontend']),
                    'show_on_checkout'  => $show_on_checkout, 
                    'display_on_backend'=>$data['display_on_backend'],
                    'store_id'          =>$store,
                    'is_custom'         =>$model->getIsCustom(),
                      ); 
                $model_id = Mage::getModel('customerattribute/customeraddressattribute')->getCollection()
                        ->addFieldToFilter('attribute_id',$attributeId)->getFirstItem()->getId();
                if($model_id){                    
                    $model->load($model_id)->setData($data_setup);
                    $model->setId($model_id)->save();                   
                }else{
                    $data_setup['is_custom']= 1;
                    $model->setData($data_setup)->save();     
                }
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_getSession()->addSuccess(
                    Mage::helper('customerattribute')->__('The customer address attribute has been saved.')
                );
                    $this->_redirect('*/*/edit', array(
                        'id'  => $attributeObject->getId(),
                        '_current'      => true
                    ));
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('customerattribute')->__('The customer address attribute has been saved.'));
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('customerattribute')->__('An error occurred while saving the customer address attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }            
        }
        $this->_redirect('*/*/');
        return;
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('id');
        $attributeObject = $this->_initAttribute()->load($attributeId);
        $model = Mage::getModel('customerattribute/customeraddressattribute')->getCollection()
                                ->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
        try {
            if($model->getIsCustom()==1){
                $attributeObject->delete();
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerattribute')->__('Attribute have been deleted'));
            }else{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerattribute')->__('Unable delete attribute system'));
             }
        } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $attributeIds = $this->getRequest()->getParam('attribute_id');
        if (!is_array($attributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $i = 0;
                $j = 0;
                foreach ($attributeIds as $attributeId) {
                    $attributeObject = $this->_initAttribute()->load($attributeId);
                    $model = Mage::getModel('customerattribute/customeraddressattribute')->getCollection()
                                    ->addFieldToFilter('attribute_id',$attributeId)->getFirstItem(); 
                    if($model->getIsCustom() ==1){
                        $attributeObject->delete();
                        $model->delete();
                        $i++;
                    }else{                       
                        $j++;
                    }
                }
                if($i != 0)
                 Mage::getSingleton('adminhtml/session')->addSuccess(
                         Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',$i)
                );
                if($j != 0)
                  Mage::getSingleton('adminhtml/session')->addError(
                          Mage::helper('adminhtml')->__('Unable delete %d record(s) of attributes system',$j));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $attributeIds = $this->getRequest()->getParam('attribute_id');
        $status = $this->getRequest()->getParam('status');
        if (!is_array($attributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {   $i = 0;
                foreach ($attributeIds as $attributeId) {
                       $attributeObject = $this->_initAttribute()->load($attributeId);
                       $model = Mage::getModel('customerattribute/customeraddressattribute')->getCollection()
						->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
                       if($model->getIsCustom() == 1 ) {
                         if($status == 1){
                            if($model->getDisplayOnBackend()== 1)
                                $data['used_in_forms'][] = 'adminhtml_customer_address';
                            if($model->getShowOnAcountAddress()== 1)
                                $data['used_in_forms'][] = 'customer_address_edit';
                            if($model->getShowOnCheckout()== 1)
                                $data['used_in_forms'][] = 'customer_register_address';
                            try{
                            $attributeObject->setData('used_in_forms', 	$data['used_in_forms']);
                            $attributeObject->save();
                            }catch (Exception $e){
                                $this->_getSession()->addError($e->getMessage());
                            }
                         }else{
                             try{
                                $attributeObject->setData('used_in_forms', array());
                                $attributeObject->save();
                             }catch (Exception $e){
                                 $this->_getSession()->addError($e->getMessage());
                             }
                         }  
                         $model->setStatus($status)->setIsMassupdate(true)->save();
                         $i++;
                       }else{
                          $this->_getSession()->addError($this->__('Unable change status attribute system'));  
                          $this->_redirect('*/*/index');
                       }                  	                                                               
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $i));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'customerattribute.csv';
        $content    = $this->getLayout()
                           ->createBlock('customerattribute/adminhtml_customeraddressattribute_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'customerattribute.xml';
        $content    = $this->getLayout()
                           ->createBlock('customerattribute/adminhtml_customeraddressattribute_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('customerattribute')->__('Attribute with the same code already exists')
                );

                $this->_initLayoutMessages('adminhtml/session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if ($data) {
            /* @var $helper Enterprise_Customer_Helper_Data */
            $helper = Mage::helper('customerattribute');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helper->stripTags($value);
                }
            }
            //options
            if (!empty($data['option']['value'])) {
                foreach ($data['option']['value'] as &$options) {
                    foreach ($options as &$label) {
                        $label = $helper->stripTags($label);
                    }
                }
            }
            //default value
            if (!empty($data['default_value_text'])) {
                $data['default_value_text'] = $helper->stripTags($data['default_value_text']);
            }
            if (!empty($data['default_value_textarea'])) {
                $data['default_value_textarea'] = $helper->stripTags($data['default_value_textarea']);
            }
        }
        return $data;
    }
}