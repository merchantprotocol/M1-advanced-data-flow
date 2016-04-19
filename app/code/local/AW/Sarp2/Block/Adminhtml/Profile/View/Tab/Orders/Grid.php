<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Orders_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awsarp2_profile_order_grid');
    }

    public function setCollection($collection)
    {
        $collection = Mage::registry('current_profile')->getLinkedOrderCollection();
        return parent::setCollection($collection);
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    protected function _prepareColumns()
    {
        //hack for fix incorrect href on View action
        $result = parent::_prepareColumns();
        $column = $this->getColumn('action');
        if (!$column) {
            return $result;
        }
        $data = $column->getData();
        $data['actions'][0]['url'] = array('base' => 'adminhtml/sales_order/view');
        $column->setData($data);
        return $result;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}