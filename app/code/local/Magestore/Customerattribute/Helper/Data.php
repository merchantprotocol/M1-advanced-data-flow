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
class Magestore_Customerattribute_Helper_Data extends Mage_Core_Helper_Abstract
{
	/*
	create by ThinhND
	*/
	public function getAttributeValidateRules1($inputType, array $data)
    {
        $inputTypes = $this->getAttributeInputTypes();
        $rules      = array();
        if (isset($inputTypes[$inputType])) {
            foreach ($inputTypes[$inputType]['validate_types'] as $validateType) {
                if (!empty($data[$validateType])) {
                    $rules[$validateType] = $data[$validateType];
                }
            }
            //transform date validate rules to timestamp
            if ($inputType === 'date') {
                foreach(array('date_range_min', 'date_range_max') as $dateRangeBorder) {
                    if (isset($rules[$dateRangeBorder])) {
                        $date = new Zend_Date($rules[$dateRangeBorder], $this->getDateFormat1());//zend_debug::dump($date);
                        $rules[$dateRangeBorder] = $date->getTimestamp();
                    }
                }
            }
            if (!empty($inputTypes[$inputType]['validate_filters']) && !empty($data['input_validation'])) {
                if (in_array($data['input_validation'], $inputTypes[$inputType]['validate_filters'])) {
                    $rules['input_validation'] = $data['input_validation'];
                }
            }
        }
        return $rules;
    }
	public function getDateFormat1()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
	public function getAttributeLabel($data)
	{
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$entityTypeId     = $setup->getEntityTypeId('customer');
		$tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
		$customerAttribute = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId);
		$customerAttribute->getSelect()
                       ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id');
		$customerAttribute->addFieldToFilter('table_attribute.is_custom',1);
		$customerAttribute->addFieldToFilter('frontend_input','date');		
		foreach($customerAttribute as $a){
		$data = $this->_filterDates($data, array($a->getAttributeCode(), 'valid_to'));
		}
		return $data;
	}
	protected function _filterDates($array, $dateFields)
       {
           if (empty($dateFields)) {
               return $array;
           }
           $filterInput = new Zend_Filter_LocalizedToNormalized(array(
              'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
           ));
           $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
               'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
           ));
  
           foreach ($dateFields as $dateField) {//zend_debug::dump(array_key_exists($dateField, $array));die();
               if (array_key_exists($dateField, $array) && !empty($dateField)) {
                   $array[$dateField] = $filterInput->filter($array[$dateField]);
                   $array[$dateField] = $filterInternal->filter($array[$dateField]);
               }
           }
           return $array;
       }
	public function getOptions($attributeId)
	{
		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeId)
                ->setPositionOrder('desc', true)
                ->load();
		$options = array();
		foreach($optionCollection as $option)
			{
				$options[$option->getOptionId()]=$option->getValue();
			}			
		return $options;
	}
        public function getOptionIds($attributeId)
	{
		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeId)
                ->setPositionOrder('desc', true)
                ->load();
		$options = array();
		foreach($optionCollection as $option)
			{
				$options[]=$option->getOptionId();
			}			
		return $options;
	}
        public function getAllOptionLabel()
        {
            $options = Mage::getResourceModel('eav/entity_attribute_option_collection')->setPositionOrder('desc', true);
            $option_labels = array();
            foreach ($options as $option){
                $option_labels[$option->getOptionId()]=$option->getValue();
            }
            return $option_labels;    
        }

        public function getYesNo($attributeId)
	{
		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeId)
                ->setPositionOrder('desc', true)
                ->load();
		$options = array();
		foreach($optionCollection as $option)
			{
				$options[$option->getOptionId()]=$option->getValue();
			}			
		return $options;
	}
	public function getValueShoOnFrontend($data){
		$result = array();
		if(count($data)){
			if($data['show_on_create_account'])
			array_push($result,1);
			if($data['show_on_account_edit'])
			array_push($result,2);
			if($data['show_on_checkout_register_customer'])
			array_push($result,3);
			if($data['show_on_checkout_register_guest'])
			array_push($result,4);
		}
		return $result;
	}
        public function getValueAddressShowOnFrontend($data){
		$result = array();
		if(count($data)){
			if($data['show_on_acount_address'])
			array_push($result,0);
			if($data['show_on_checkout'])
			array_push($result,1);			
		}
		return $result;
	}
	public function getValueShoOnBackend($data){
		$result = array();
		if(count($data)){
			if($data['show_on_grid_customer'])
			array_push($result,1);
			if($data['show_on_grid_order'])
			array_push($result,2);
			if($data['show_on_admin_checkout'])
			array_push($result,3);
		}
		return $result;
	}
	public function getValueShowOnStore($data){
		$result = explode(",", $data['store_id']);
		return $result;
	}
	public function getValueShowOnGroup($data){
		$result = explode(',',$data['customer_group']);
		return $result;
	}
	public function getGroups()
    {
        $customer_group = new Mage_Customer_Model_Group();
		$allGroups  = $customer_group->getCollection()->toOptionHash();
		$data=array();
		foreach($allGroups as $label => $allGroup)
		{
			$data[]=array('label'=>$allGroup,'value'=>$label);
		}
        return $data;
    }
	/* End*/
    public function getType(){
		foreach ( $model as $value)
        {
            $options[$i]= $value->getName();
            $i++;
        }   
        return $options;
	}
    public function getShowOnFrontend()
    {		
            $options = array();
		
            $options[] = array(
                'value' => 1,
                'label' => "Show on Registration page"
            );
			$options[] = array(
                'value' => 2,
                'label' => "Show on Account Manager page"
            );
			$options[] = array(
                'value' => 3,
                'label' => "Show on Checkout page (Register)"
            );
			$options[] = array(
                'value' => 4,
                'label' => "Show on Checkout page (Guest)"
            );
		
		return $options;
	}
    public function getShowAddressOnFrontend()
        {
            $options = array();
            $options[] = array(
                'value' => 0,
                'label' => "Customer Account Address"
            );
            $options[] = array(
                'value' => 1,
                'label' => "Checkout Address Registration"
            );		
            return $options; 
        }

    public function getShowOnBackend()
    {		
		$options = array();
		
            $options[] = array(
                'value' => 1,
                'label' => "Show on Customer Grid"
            );
			$options[] = array(
                'value' => 2,
                'label' => "Show on Order Grid"
            );
			$options[] = array(
                'value' => 3,
                'label' => "Show on Admin Checkout page"
            );
		
		return $options;
	}
    public function getShowAddressOnBackend()
        {
            $options = array();
            $options[] = array(
                'value' => 0,
                'label' => "Admin Create Order"
            );
            $options[] = array(
                'value' => 1,
                'label' => "Admin Customer Address Edit"
            );		
            return $options; 
        }
    public function getFrontendInputOptions()
    {
        $inputTypes = $this->getAttributeInputTypes();
        $options    = array();
        foreach ($inputTypes as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v['label']
            );
        }

        return $options;
    }
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'text'          => array(
                'label'             => Mage::helper('customerattribute')->__('Text Field'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(
                    'alphanumeric',
                    'numeric',
                    'alpha',
                    'url',
                    'email',
                ),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'varchar',
                'default_value'     => 'text',
            ),
            'textarea'      => array(
                'label'             => Mage::helper('customerattribute')->__('Text Area'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'text',
                'default_value'     => 'textarea',
            ),            
            'date'          => array(
                'label'             => Mage::helper('customerattribute')->__('Date'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'date_range_min',
                    'date_range_max'
                ),
                'validate_filters'  => array(
                    'date'
                ),
                'filter_types'      => array(
                    'date'
                ),
                'backend_model'     => 'eav/entity_attribute_backend_datetime',
                'backend_type'      => 'datetime',
                'default_value'     => 'date',
            ),
            'select'        => array(
                'label'             => Mage::helper('customerattribute')->__('Dropdown'),
                'manage_options'    => true,
                'option_default'    => 'radio',
                'validate_types'    => array(),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'source_model'      => 'eav/entity_attribute_source_table',
                'backend_type'      => 'int',
                'default_value'     => false,
            ),
            'multiselect'   => array(
                'label'             => Mage::helper('customerattribute')->__('Multiple Select'),
                'manage_options'    => true,
                'option_default'    => 'checkbox',
                'validate_types'    => array(),
                'filter_types'      => array(),
                'validate_filters'  => array(),
                'backend_model'     => 'eav/entity_attribute_backend_array',
                'source_model'      => 'eav/entity_attribute_source_table',
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
            'boolean'       => array(
                'label'             => Mage::helper('customerattribute')->__('Yes/No'),
                'manage_options'    => false,
                'validate_types'    => array(),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'source_model'      => 'eav/entity_attribute_source_boolean',
                'backend_type'      => 'int',
                'default_value'     => 'yesno',
            ),
            'file'          => array(
                'label'             => Mage::helper('customerattribute')->__('File (attachment)'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'max_file_size',
                    'file_extensions'
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
            'image'         => array(
                'label'             => Mage::helper('customerattribute')->__('Image File'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'max_file_size',
                    'max_image_width',
                    'max_image_heght',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }
   
   // Create by Hoatq
    public function getAttributeValidateFilters()
    {
        return array(
            'alphanumeric'  => Mage::helper('customerattribute')->__('Alphanumeric'),
            'numeric'       => Mage::helper('customerattribute')->__('Numeric Only'),
            'alpha'         => Mage::helper('customerattribute')->__('Alpha Only'),
            'url'           => Mage::helper('customerattribute')->__('URL'),
            'email'         => Mage::helper('customerattribute')->__('Email'),
            'date'          => Mage::helper('customerattribute')->__('Date'),
        );
    }
    public function getAttributeFilterTypes()
    {
        return array(
            'striptags'     => Mage::helper('customerattribute')->__('Strip HTML Tags'),
            'escapehtml'    => Mage::helper('customerattribute')->__('Escape HTML Entities'),
            'date'          => Mage::helper('customerattribute')->__('Normalize Date')
        );
    }
    public function getAttributeDefaultValueByInput($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (isset($inputTypes[$inputType])) {
            $value = $inputTypes[$inputType]['default_value'];
            if ($value) {
                return 'default_value_' . $value;
            }
        }
        return false;
    }
    public function getAttributeValidateRules($inputType, array $data)
    {
        $inputTypes = $this->getAttributeInputTypes();
        $rules      = array();
        if (isset($inputTypes[$inputType])) {
            foreach ($inputTypes[$inputType]['validate_types'] as $validateType) {
                if (!empty($data[$validateType])) {
                    $rules[$validateType] = $data[$validateType];
                }
            }
            //transform date validate rules to timestamp
            if ($inputType === 'date') {
                foreach(array('date_range_min', 'date_range_max') as $dateRangeBorder) {
                    if (isset($rules[$dateRangeBorder])) {
                        $date = new Zend_Date($rules[$dateRangeBorder], $this->getDateFormat());//zend_debug::dump($date);
                        $rules[$dateRangeBorder] = $date->getTimestamp();
                    }
                }
            }
            if (!empty($inputTypes[$inputType]['validate_filters']) && !empty($data['input_validation'])) {
                if (in_array($data['input_validation'], $inputTypes[$inputType]['validate_filters'])) {
                    $rules['input_validation'] = $data['input_validation'];
                }
            }
        }
        return $rules;
    }
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }
    public function getAttributeBackendTypeByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_type'])) {
            return $inputTypes[$inputType]['backend_type'];
        }
        return null;
    }
     public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
    
    /*code by Thin*/
    /**
     * upload image
     * @return type
     */public function escapeUrl($data) {
        parent::escapeUrl($data);
    }
    public static function uploadImage($sizeWidth = null, $sizeHeight = null) {
        $image_path = Mage::getBaseDir('media') . DS . 'customerattribute/image';
        $image_file_name = "";
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('image');
                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $result = $uploader->save($image_path, $uploader->getCorrectFileName($_FILES['image']['name']));
                
                $image_file_name = substr(strrchr($uploader->getUploadedFileName(), "/"), 1);
                
                //resize image
                if($result){
                    $image = new Varien_Image($image_path+'/'+$image_file_name);
                    if($sizeWidth != null){
                        $image->resize($sizeWidth, $sizeHeight);
                        $image->save();
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $image_file_name;
    }
    public function getOrderCollection($storeId,$created_at){      
        $orders = array();
        foreach (Mage::getResourceModel('sales/order_grid_collection') as $order){
            $time = date("Y-m-d",strtotime($order->getCreatedAt()));
            if($storeId != 0){                
                if($order->getStoreId() == $storeId){
                    if(!empty($created_at['from']) && !empty($created_at['to'])){
                        if(($time >= $created_at['from'])&&($time<= $created_at['to']))
                           $orders[]=  $order->getData();
                    }elseif(empty($created_at['from']) && !empty($created_at['to'])){
                        if($time<= $created_at['to']) 
                            $orders[]=  $order->getData();
                    }elseif(!empty($created_at['from']) && empty($created_at['to'])){
                        if($time>= $created_at['from'])
                            $orders[]=  $order->getData();
                    }elseif(empty($created_at['from']) && empty($created_at['to'])){
                        $orders[]=  $order->getData();
                    }   
                }  
           }else{
                if(!empty($created_at['from']) && !empty($created_at['to'])){
                    if(($time >= $created_at['from'])&&($time<= $created_at['to'])) 
                        $orders[]=  $order->getData();
                }elseif(empty($created_at['from']) && !empty($created_at['to'])){
                    if($time<= $created_at['to']) 
                        $orders[]=  $order->getData();
                }elseif(!empty($created_at['from']) && empty($created_at['to'])){
                    if($time>= $created_at['from']) 
                        $orders[]=  $order->getData();
                }elseif(empty($created_at['from']) && empty($created_at['to'])){
                    $orders[]=  $order->getData();
                }   
           } 
        }
        return $orders;
    }

    public function getNumberOrder($customerId,$status,$orderCollection){
        $data = array();
        $number=0;
        foreach ($orderCollection as $order){
            if(!empty($status) && $order['customer_id'] == $customerId && $order['status'] == $status)
            {
                $number++;
                $data['orderIds'][]=$order['entity_id'];
            }
            if(empty($status) && $order['customer_id'] == $customerId)
            {
                $number++;
                $data['orderIds'][]=$order['entity_id'];
            }
        }
        $data['number'] = $number;
        return $data;
    } 
    public function getAllOrderStatus()
    {
        return array(
            'pending'=>'Pending',
            'pending_payment'=>'Pending Payment',
            'processing'=>'Processing',
            'holded'=>'On Hold',
            'complete'=>'Complete',
            'closed'=>'Closed',
            'canceled'=>'Canceled',
            'fraud'=>'Suspected Fraud',
            'payment_review'=>'Payment Review',
            'pending_paypal'=>'Pending PayPal',
        );
    }
}