<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Class Magestore_Customerattribute_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{   
    protected function _getCollectionClass()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $order =  array();
        foreach($collection as $collect){
            $order_id = $collect->getId();
            $customer_id = $collect->getData('customer_id');           
            $customer = Mage::getModel('customer/customer')->load($customer_id)->getData();
            $customer_attributes = Mage::getModel('customer/attribute')->getCollection();
            foreach($customer_attributes as $customer_attribute){
                $code = 'customer_'.$customer_attribute->getAttributeCode();
                $order[$code] = $customer[$customer_attribute->getAttributeCode()];
            }
            $order['order_id']= $order_id;
            $orderattribute = Mage::getModel('customerattribute/orderattribute')->getCollection()
                                    ->addFieldToFilter('order_id',$order_id)->getFirstItem();
            if(!$orderattribute->getId())
            {
              Mage::getModel('customerattribute/orderattribute')->setData($order)->save();
            }
        }       
        return 'sales/order_grid_collection';
    }
    protected function _prepareCollection()
   {    
        $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('orderattribute');
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()
            ->join(array('table_order'=>$tbl_faq_item),'main_table.entity_id=table_order.order_id');   
        $this->setCollection($collection);
       return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    protected function _prepareColumns()
    {   
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));
        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));       
  //  Add Column Customer Attribute to Order grid create by Hoatq      
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $entityTypeId     = $setup->getEntityTypeId('customer');
    $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('customerattribute/customerattribute');
    $models = Mage::getModel('customer/attribute')->getCollection();
    $models->getSelect()
           ->join(array('table_attribute'=>$tbl_faq_item),'main_table.attribute_id=table_attribute.attribute_id');//->addFieldToFilter('main_table.attribute_id',$customerattributeId);
    $models->addFieldToFilter('show_on_grid_order',1);   
     $i = 0;
     if($i==0){
        foreach ($models as $model){ 
            $index = 'customer_'.$model->getAttributeCode();
            $frontend_input = $model->getFrontendInput();
            if($frontend_input !='file' && $frontend_input != 'image'){
                if($frontend_input == 'boolean'){
                     $this->addColumn($index, array(
                        'header' => Mage::helper('sales')->__('Customer '.str_replace('_',' ',$model->getAttributeCode())),
                        'index'  => $index,                         
                        'align'  =>'center',          
                        'type'	 => 'options',
                        'options'	=> array(
                                            0 => 'No',
                                            1 => 'Yes',
                                             ),                      
                                ));
                }elseif($frontend_input == 'multiselect'){
                     $this->addColumn($index, array(
                        'header'   => Mage::helper('sales')->__('Customer '.str_replace('_',' ',$model->getAttributeCode())),
                        'index'    => $index,                         
                        'align'    =>'center',   
                        'type'		=> 'options',
			'options'	=> Mage::helper('customerattribute')->getOptions($show['attribute_id']),
                        'renderer' => 'Magestore_Customerattribute_Block_Adminhtml_Sales_Order_Renderer_Multiselect',
                        'filter'   => false,
                                )); 
                }elseif($frontend_input == 'select'){
               $this->addColumn($index, array(
                  'header'   => Mage::helper('sales')->__('Customer '.str_replace('_',' ',$model->getAttributeCode())),
                  'index'    => $index,
                  'type'     => 'options',
                  'options'  => Mage::helper('customerattribute')->getOptions($model->getId()),
                      )); 
                }elseif($frontend_input == 'date'){
               $this->addColumn($index, array(
                  'header'   => Mage::helper('sales')->__('Customer '.str_replace('_',' ',$model->getAttributeCode())),
                  'index'    => $index,
                  'type'     => 'date',
                  'format'	=>Mage::helper('customerattribute')->getDateFormat1(),
                   'filter'   => false,
                      )); 
                }else{
                    $this->addColumn($index, array(
                        'header' => Mage::helper('sales')->__('Customer '.str_replace('_',' ',$model->getAttributeCode())),
                        'index' => $index
                    ));
                }
            }
        }
        $i++;
     }
  // -----------------------------------  
        
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
        return $this;
    }
}
?>
