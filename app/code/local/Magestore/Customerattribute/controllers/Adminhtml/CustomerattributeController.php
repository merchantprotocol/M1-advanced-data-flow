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
 * Customerattribute Adminhtml CustomerattributeController
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Adminhtml_CustomerattributeController extends Mage_Adminhtml_Controller_Action
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
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        }
        return $this->_entityType;
    }
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/customerattribute/customerattribute')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
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
        $customerattributeId     = $this->getRequest()->getParam('id');
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$entityTypeId     = $setup->getEntityTypeId('customer');
		$tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
		$models = Mage::getModel('customer/attribute')->getCollection();
		
		$models->getSelect()
                       ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id');//->addFieldToFilter('main_table.attribute_id',$customerattributeId);
					   $models->addFieldToFilter('main_table.attribute_id',$customerattributeId);
		$model=$models->getFirstItem();
		
        if ($model->getAttribute_id() || $customerattributeId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('customerattribute_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('customer/customerattribute/customerattribute');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('customerattribute/adminhtml_customerattribute_edit'))
                ->_addLeft($this->getLayout()->createBlock('customerattribute/adminhtml_customerattribute_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('customerattribute')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
	
	public function getCondition($data)
	{
		$condition=array();
		if(!empty($data['min_text_length']))
			$condition['min_text_length']=$data['min_text_length'];
		if(!empty($data['max_text_length']))
			$condition['max_text_length']=$data['max_text_length'];
			
		if(!empty($data['max_file_size']))
			$condition['max_file_size']=$data['max_file_size'];
			
		if(!empty($data['date_range_min']))
			$condition['date_range_min']=$data['date_range_min'];
		if(!empty($data['date_range_max']))
			$condition['date_range_max']=$data['date_range_max'];
		
		if(!empty($data['max_image_width']))
			$condition['max_image_width']=$data['max_image_width'];
		if(!empty($data['max_image_heght']))
			$condition['max_image_heght']=$data['max_image_heght'];
		if(!empty($data['input_validation']))
			$condition['input_validation']=$data['input_validation'];
		$code=serialize($condition);
		return $code;
	}
    public function saveAction()
    {
		if ($data = $this->getRequest()->getPost()) {	
			$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
			$entityTypeId     = $setup->getEntityTypeId('customer');
			$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
			$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
			$attributeObject = Mage::getModel('customer/attribute');
			$attributeId = $this->getRequest()->getParam('id');
			$helper = Mage::helper('customerattribute');
			$attributeType=$attributeObject->load($attributeId)->getFrontendInput();
			if($attributeId!=null)
			$data['frontend_input']=$attributeType;
			$data['validate_rules']=$helper->getAttributeValidateRules1($data['frontend_input'], $data);		
			
			if($data['is_custom']==1||$attributeId==0)
			{
			if($data['status']==1)
			{
			if(in_array(1,$data['display_on_frontend']))
			$data['used_in_forms'][]='customer_account_create';
			if(in_array(2,$data['display_on_frontend']))
			$data['used_in_forms'][]='customer_account_edit';
			if((in_array(3,$data['display_on_frontend'])||in_array(4,$data['display_on_frontend']))&&$attributeType!='file'&&$attributeType!='image')
			$data['used_in_forms'][]='checkout_register';
			if(in_array(1,$data['display_on_backend']))
			$data['used_in_forms'][]='customer_Grid';
			if(in_array(2,$data['display_on_backend']))
			$data['used_in_forms'][]='order_grid';
			if(in_array(3,$data['display_on_backend'])&&$attributeType!='file'&&$attributeType!='image')
			$data['used_in_forms'][]='adminhtml_checkout';
			$data['used_in_forms'][]='tabCustomerattribute';
			} else
			{
				$data['used_in_forms'][]= array();
			}
			}
			try{
			$data1 = array(
			
				'status'=>$data['status'],
				'show_on_create_account'=>(int)in_array(1,$data['display_on_frontend']),
				'show_on_account_edit'=>(int)in_array(2,$data['display_on_frontend']),
				'show_on_checkout_register_customer'=>(int)in_array(3,$data['display_on_frontend']),
				'show_on_checkout_register_guest'=>(int)in_array(4,$data['display_on_frontend']),
				'show_on_grid_customer'=>(int)in_array(1,$data['display_on_backend']),
				'show_on_grid_order'=>(int)in_array(2,$data['display_on_backend']),
				'show_on_admin_checkout'=>(int)in_array(3,$data['display_on_backend']),
				'customer_group'=>implode(", ",$data['customer_group']),
				'store_id'=>implode(", ",$data['store_view'])
				);
				if($attributeType=='file'||$attributeType=='image')
				{
					$data1['show_on_checkout_register_customer']=0;
					$data1['show_on_checkout_register_guest']=0;
					$data1['show_on_admin_checkout']=0;
				}
				$model = Mage::getModel('customerattribute/customerattribute');
				if($attributeId){
				$models = Mage::getModel('customerattribute/customerattribute')->getCollection()
						->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
				$data1['attribute_id']=$attributeId;
					$attributeObject->load($attributeId)->addData($data);
					$attributeObject->setId($attributeId)->save();
					$model->load($models->getId())->setData($data1);
							$model->setId($models->getId())->save();
							Mage::getSingleton('adminhtml/session')->addSuccess(
							Mage::helper('customerattribute')->__('The customer attribute has been saved.'));
					
				}
				else{
				$data['attribute_code']=str_replace(' ','',$data['attribute_code']);
				$data['attribute_code']= strtolower($data['attribute_code']);
					$defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
					if ($defaultValueField) {
						$scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
						$data[$scopeKeyPrefix . 'default_value'] = $helper->stripTags(
							$this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField));
					}
					$data['entity_type_id']=$entityTypeId;
					$data['attribute_group_id']=$attributeGroupId;
					$data['attribute_set_id']=$attributeSetId;
					$data['backend_type']=$helper->getAttributeBackendTypeByInputType($data['frontend_input']);
					$data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
					$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
					$data['is_system']=0;
					$data['is_user_defined']=1;
					if($data['frontend_input']=='date')
					$data['input_filter']='date';
					
					$attributeObject->setData($data);
					$attributeId=$attributeObject->save()->getId();
					$data1['is_custom']=1;
					$data1['attribute_id']=$attributeId;
					$model->setData($data1)->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('customerattribute')->__('The customer customer attribute has been saved.'));
                                        // Addcolumn to orderattribute table create by Hoatq
                                        $table = new Mage_Core_Model_Resource_Setup;
                                        $table->getConnection()->addColumn($table
                                            ->getTable('orderattribute'), 'customer_'.$data['attribute_code'], 'varchar(255)');
				}
			} catch (Exception $e) {
			
				if (!$attributeId) {
					$attributeCode      = $data['attribute_code'];
					$attributeObject    = $this->_initAttribute()
						->loadByCode($this->_getEntityType()->getId(), $attributeCode);
					if ($attributeObject->getId()) {
						$this->_getSession()->addError(
							Mage::helper('customerattribute')->__('Attribute with the same code already exists')
						);
					}
				}else{
					Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('customerattribute')->__('Unable to find item to save'));
				}
			} 
			if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'id'  => $attributeId,
                        '_current'      => true
                    ));
					return;
                } else {
                    $this->_redirect('*/*/');
					return;
                }
		}
		else{
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('customerattribute')->__('Unable to find item to save')
        );
		}
        $this->_redirect('*/*/');
		return;
	}
 
    /**
     * delete item action
     */
	 
	 protected function _initAttribute()
    {
        $attribute = Mage::getModel('customer/attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
		
        return $attribute;
    }
	public function deleteAction(){
		$attributeId = $this->getRequest()->getParam('id');
		$model = Mage::getModel('customerattribute/customerattribute')->getCollection()
							->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
		if($model->getIsCustom())
			{
				$attributeObject = $this->_initAttribute()->load($attributeId);
				$model = Mage::getModel('customerattribute/customerattribute')->getCollection()
							->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
		try {
                $attributeObject->delete();
				$model->delete();
			} catch (Exception $e) {
				 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Customer attribute were successful deleted'));
			}
			else
			{
			 Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__('Customer attribute can not deleted'));
			}
			$this->_redirect('*/*/');
	}
    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $customerattributeIds = $this->getRequest()->getParam('attribute_id');
        if (!is_array($customerattributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
				$totalAttribute = 0;
                foreach ($customerattributeIds as $attributeId) {
                    $attributeObject = $this->_initAttribute()->load($attributeId);
					$model = Mage::getModel('customerattribute/customerattribute')->getCollection()
							->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
					if($model->getIsCustom())
					{
					$attributeObject->delete();
					$model->delete();
					$totalAttribute++;
					}
                }
				if($totalAttribute>0)
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    $totalAttribute));
				else
				Mage::getSingleton('adminhtml/session')->add(
                    Mage::helper('adminhtml')->__('Total of %d record(s) can not deleted',
                    count($customerattributeIds)));
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
        $customerattributeIds = $this->getRequest()->getParam('attribute_id');//zend_debug::dump($this->getRequest()->getParam('status'));die();
        
		
		$status = $this->getRequest()->getParam('status');
		if (!is_array($customerattributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
				$totalAttribute = 0;
                foreach ($customerattributeIds as $attributeId) {
						$model = Mage::getModel('customerattribute/customerattribute')->getCollection()
						->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
						if($model->getIsCustom()){
						$attributeObject = $this->_initAttribute()->load($attributeId);
						if($status == 2)
						{
							try{
								$attributeObject = $this->_initAttribute()->load($attributeId);
								$attributeObject->setData('used_in_forms', array());
								$attributeObject->save();
							}catch(Exception $e)
							{
								Mage::getSingleton('adminhtml/session')->addError(
								Mage::helper('customerattribute')->__('Unable to change status.'));
							}
						}else if($status == 1)
						{
							$model1 = Mage::getModel('customerattribute/customerattribute')->getCollection()
							->addFieldToFilter('attribute_id',$attributeId)->getFirstItem();
								if($model1->getShowOnCreateAccount())
								$data['used_in_forms'][]='customer_account_create';
								if($model1->getShowOnAccountEdit())
								$data['used_in_forms'][]='customer_account_edit';
								if($model1->getShowOnCheckoutRegisterCustomer())
								$data['used_in_forms'][]='checkout_register';
								
								if($model1->getShowOnGridCustomer())
								$data['used_in_forms'][]='customer_Grid';
								if($model1->getShowOnGridOrder())
								$data['used_in_forms'][]='order_grid';
								if($model1->getShowOnAdminCheckout())
								$data['used_in_forms'][]='adminhtml_checkout';
								$data['used_in_forms'][]='tabCustomerattribute';
								$attributeObject->setData('used_in_forms', 	$data['used_in_forms']);
								$attributeObject->save();
						}
                    	$model->setStatus($status)->setIsMassupdate(true)->save();
						$totalAttribute++;
                }
				}
				if($totalAttribute>0)
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $totalAttribute));
				else 
				$this->_getSession()->addError(
                    $this->__('Total of %d record(s) can not updated', count($customerattributeIds))
                );
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
                           ->createBlock('customerattribute/adminhtml_customerattribute_grid')
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
                           ->createBlock('customerattribute/adminhtml_customerattribute_grid')
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
}