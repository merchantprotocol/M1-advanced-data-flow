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
 * Customerattribute Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function testAction()
    {
        $order_data = Mage::getModel('sales/order')->load(1)->getData();  
        $data = array();
        foreach($order_data as $label=>$value){
            if($label != 'entity_id' && $label != 'customer_id')
                $data[$label] = $value;
        }
        $data['customer_id']=2;
        //Zend_debug::dump($data);die();
        for($i=0;$i++;$i<20){
            Mage::getModel('sales/order_grid')->setData($data)->save();
        }
    }
}