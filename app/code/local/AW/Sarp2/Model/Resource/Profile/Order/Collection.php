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


class AW_Sarp2_Model_Resource_Profile_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_sarp2/profile_order');
    }

    public function addProfileFilter($profile)
    {
        if ($profile instanceof AW_Sarp2_Model_Profile) {
            $profileId = $profile->getId();
        } elseif (is_numeric($profile)) {
            $profileId = $profile;
        } else {
            return $this;
        }
        $this->addFieldToFilter('profile_id', $profileId);
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function getLinkedOrderCollection()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS)->columns('order_id');
        $orderIds = $this->getConnection()->fetchCol($select);
        $orderCollection = Mage::getResourceModel('sales/order_grid_collection');
        $orderCollection->addAttributeToFilter('entity_id', array('in' => $orderIds));
        return $orderCollection;
    }
}