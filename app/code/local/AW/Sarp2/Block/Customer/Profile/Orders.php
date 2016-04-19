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

class AW_Sarp2_Block_Customer_Profile_Orders extends Mage_Core_Block_Template
{
    /**
     * Retrieve current profile model instance
     *
     * @return AW_Sarp2_Model_Profile
     */
    public function getProfile()
    {
        return Mage::registry('current_profile');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getOrderCollection());
        $this->setChild('pager', $pager);
        $this->getOrderCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getOrderCollection()
    {
        return $this->getProfile()
            ->getLinkedOrderCollection()
            ->addAttributeToSort('created_at', Varien_Data_Collection::SORT_ORDER_DESC)
        ;
    }

    public function getOrderLink($order)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $orderId = $order->getId();
        } elseif (is_numeric($order)) {
            $orderId = $order;
        } else {
            return null;
        }
        return $this->getUrl('sales/order/view/', array('order_id' => $orderId));
    }
}