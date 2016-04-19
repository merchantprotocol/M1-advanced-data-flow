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
class Magestore_Customerattribute_Block_Customer_Form extends Mage_Core_Block_Template
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customerattribute_customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'customer/form';
    
    
    
    
    protected $_renderBlockTypes    = array();

    /**
     * Array of renderer blocks keyed by attribute front-end type
     *
     * @var array
     */
    protected $_renderBlocks        = array();

    /**
     * EAV Form Type code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Entity model class type for new entity object
     *
     * @var string
     */
    protected $_entityModelClass;

    /**
     * Entity type instance
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * EAV form instance
     *
     * @var Mage_Eav_Model_Form
     */
    protected $_form;

    /**
     * EAV Entity Model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Format for HTML elements id attribute
     *
     * @var string
     */
    protected $_fieldIdFormat   = '%1$s';

    /**
     * Format for HTML elements name attribute
     *
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * Add custom renderer block and template for rendering EAV entity attributes
     *
     * @param string $type
     * @param string $block
     * @param string $template
     
     */
    protected $_showFieldSet = TRUE;
    
    public function addRenderer($type, $block, $template)
    {
        $this->_renderBlockTypes[$type] = array(
            'block'     => $block,
            'template'  => $template,
        );

        return $this;
    }

    /**
     * Try to get EAV Form Template Block
     * Get Attribute renderers from it, and add to self
     *    
     * @throws Mage_Core_Exception
     */
    public function _prepareLayout()
    {
        
        if (empty($this->_xmlBlockName)) {
            Mage::throwException(Mage::helper('mage_eav')->__('Current module XML block name is undefined'));
        }
        if (empty($this->_formModelPath)) {
            Mage::throwException(Mage::helper('mage_eav')->__('Current module form model pathname is undefined'));
        }
        
        $template = $this->getLayout()->getBlock($this->_xmlBlockName);        
        if ($template) {
            foreach ($template->getRenderers() as $type => $data) {
                $this->addRenderer($type, $data['block'], $data['template']);
            }
        }
        return parent::_prepareLayout();
    }
    public function loadTemplate()
    {
        
        if (empty($this->_xmlBlockName)) {
            Mage::throwException(Mage::helper('mage_eav')->__('Current module XML block name is undefined'));
        }
        if (empty($this->_formModelPath)) {
            Mage::throwException(Mage::helper('mage_eav')->__('Current module form model pathname is undefined'));
        }
        
        $template = $this->getParentBlock()->getLayout()->getBlock($this->_xmlBlockName);     
        
        if ($template) {            
            foreach ($template->getRenderers() as $type => $data) {              
                $this->addRenderer($type, $data['block'], $data['template']);
            }
        }
        
    }

    /**
     * Return attribute renderer by frontend input type
     *
     * @param string $type
     * @return Magestore_Customerattribute_Block_Form_Renderer_Abstract
     */
    public function getRenderer($type)
    {
        if (!isset($this->_renderBlocks[$type])) {
            if (isset($this->_renderBlockTypes[$type])) {
                $data   = $this->_renderBlockTypes[$type];
                if($this->getParentBlock())
                $block  = $this->getParentBlock()->getLayout()->createBlock($data['block']);
                else 
                $block  = $this->getLayout()->createBlock($data['block']);
            
                if ($block) {
                    $block->setTemplate($data['template']);
                }
            } else {
                $block = false;
            }
            $this->_renderBlocks[$type] = $block;
        }
        return $this->_renderBlocks[$type];
    }

    /**
     * Set Entity object
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return Magestore_Customerattribute_Block_Customer_Form
     */
    public function setEntity(Mage_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Set entity model class for new object
     *
     * @param string $model
     * @return Magestore_Customerattribute_Block_Customer_Form
     */
    public function setEntityModelClass($model)
    {
        $this->_entityModelClass = $model;
        return $this;
    }

    /**
     * Set Entity type if entity model entity type is not defined or is different
     *
     * @param int|string|Mage_Eav_Model_Entity_Type $entityType
     * @return Magestore_Customerattribute_Block_Customer_Form
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($entityType);
        return $this;
    }

    /**
     * Return Entity object
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            if ($this->_entityModelClass) {
                $this->_entity = Mage::getModel($this->_entityModelClass);
            }
        }
        return $this->_entity;
    }

    /**
     * Set EAV entity form instance
     *
     * @param Mage_Eav_Model_Form $form
     * @return Mage_Eav_Block_Form
     */
    public function setForm(Mage_Eav_Model_Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Set EAV entity Form code
     *
     * @param string $code
     * @return Magestore_Customerattribute_Block_Customer_Form
     */
    public function setFormCode($code)
    {
        $this->_formCode = $code;
        return $this;
    }
    public function  getFormCode(){
        return $this->_formCode;
    }

    /**
     * Return EAV entity Form instance
     *
     * @return Mage_Eav_Model_Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = Mage::getModel($this->_formModelPath)
                ->setFormCode($this->_formCode)
                ->setEntity($this->getEntity());
            if ($this->_entityType) {
                $this->_form->setEntityType($this->_entityType);
            }
            $this->_form->initDefaultValues();
        }
        return $this->_form;
    }

    /**
     * Check EAV entity form has User defined attributes
     *
     * @return boolean
     */
    public function hasUserDefinedAttributes()
    {
        return count($this->getUserDefinedAttributes()) > 0;
    }

   /**
	 *filter for group id
	**/
	public function filterGroup($attribute){
            
                if($attribute->getEntityTypeId()==2) return true;
                
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$groupId = $customer->getGroupId();
			
			$modelCustomerAttribute = Mage::getModel('customerattribute/customerattribute')->getCollection();
			$customerAttributes = $modelCustomerAttribute->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));
			$customerAttributes = $customerAttributes->addFieldToFilter('customer_group',array('like'=>'%'.$groupId.'%'));
			
			if(count($customerAttributes)>0) return true;
		}
		else{
			$groupId = Mage::getStoreConfig('customer/create_account/default_group');
			
			$modelCustomerAttribute = Mage::getModel('customerattribute/customerattribute')->getCollection();
			$customerAttributes = $modelCustomerAttribute->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));
			$customerAttributes = $customerAttributes->addFieldToFilter('customer_group',array('like'=>'%'.$groupId.'%'));
			
			if(count($customerAttributes)>0) return true;
		}
		return false;
	}
	/**
	*filter for store id
	**/
	public function filterStore($attribute){
	//crazy code :D
		$modelCustomerAttribute = Mage::getModel('customerattribute/customerattribute')->getCollection();
		$modelCustomerAddress   = Mage::getModel('customerattribute/customeraddressattribute')->getCollection();
		
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$customerAttributes = $modelCustomerAttribute->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));		
		$customerAttributes = $customerAttributes->addFieldToFilter('store_id',array('like'=>'%'.'0'.'%'));
		if(count($customerAttributes)==0){
		$modelCustomerAttribute = Mage::getModel('customerattribute/customerattribute')->getCollection();
		$customerAttributes = $modelCustomerAttribute->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));
		$customerAttributes = $customerAttributes->addFieldToFilter('store_id',array('like'=>'%'.$storeId.'%'));
		
		}
		$customerAddress =    $modelCustomerAddress->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));
		$customerAddress = $customerAddress->addFieldToFilter('store_id',array('like'=>'%'.'0'.'%'));
		if(count($customerAddress)==0){
		$modelCustomerAddress   = Mage::getModel('customerattribute/customeraddressattribute')->getCollection();
		$customerAddress =    $modelCustomerAddress->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()));
		$customerAddress = $customerAddress->addFieldToFilter('store_id',array('like'=>'%'.$storeId.'%'));
		}
		
		if(count($customerAttributes)>0){
			return true;
		}
		elseif(count($customerAddress)>0){
			return true;
		}
	return false;
	}
	
    /**
     * Return array of user defined attributes
     * modelCustomerAddress
     * @return array
     */
	
    public function getUserDefinedAttributes()
    {
        $attributes = array();
        foreach ($this->getForm()->getUserAttributes() as $attribute) {
            if ($this->getExcludeFileAttributes() && in_array($attribute->getFrontendInput(), array('image', 'file'))) {
                continue;
            }
            if (($attribute->getIsVisible())&&($this->filterStore($attribute))&&($this->filterGroup($attribute))) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $attributes;
    }
	

    /**
     * Render attribute row and return HTML
     *
     * @param Mage_Eav_Model_Attribute $attribute
     * @return string
     */
    public function getAttributeHtml(Mage_Eav_Model_Attribute $attribute)
    {   
        $showOnGuest = TRUE;
        $setup =new  Mage_Eav_Model_Entity_Setup('core_setup');
        $entityTypeId = $setup->getEntityTypeId('customer');
        if($attribute->getEntityTypeId()==$entityTypeId){
            $collection = Mage::getModel('customerattribute/customerattribute')->getCollection()
            ->addFieldToFilter('attribute_id',$attribute->getAttributeId())->getFirstItem();
            if($collection->getShowOnCheckoutRegisterGuest()==0) $showOnGuest=FALSE;
        }

        $type   = $attribute->getFrontendInput();
        $block  = $this->getRenderer($type);
        if ($block) {
            $block->setAttributeObject($attribute)
                ->setEntity($this->getEntity())
                ->setFieldIdFormat($this->_fieldIdFormat)
                ->setFieldNameFormat($this->_fieldNameFormat)
                ->setShowOnGuest($showOnGuest);
            return $block->toHtml();
        }
        return false;
    }

    /**
     * Set format for HTML elements id attribute
     *
     * @param string $format
     
     */
    public function setFieldIdFormat($format)
    {
        $this->_fieldIdFormat = $format;
        return $this;
    }

    /**
     * Set format for HTML elements name attribute
     *
     * @param string $format
     
     */
    public function setFieldNameFormat($format)
    {
        $this->_fieldNameFormat = $format;
        return $this;
    }

    /**
     * Check is show HTML container
     *
     * @return boolean
     */
    public function isShowContainer()
    {
        if ($this->hasData('show_container')) {
           
            return $this->getData('show_container');
        }
        return true;
    }
    
    public function setShowFieldSet($value){
        $this->_showFieldSet=$value;
    }
    public function isShowFieldSet(){
        return $this->_showFieldSet;
    }
}
    
