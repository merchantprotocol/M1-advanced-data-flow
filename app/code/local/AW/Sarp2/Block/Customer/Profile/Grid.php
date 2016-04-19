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

class AW_Sarp2_Block_Customer_Profile_Grid extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_prepareCollection();
    }

    protected function _prepareCollection()
    {
        $customer = $this->getCustomer();
        $profileCollection = Mage::getResourceModel('aw_sarp2/profile_collection')
            ->addCustomerFilter($customer)
            ->addSortByCreatedAt()
        ;
        $this->setCollection($profileCollection);
        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getProfileLink($profile)
    {
        if ($profile instanceof AW_Sarp2_Model_Profile) {
            $profileId = $profile->getId();
        } elseif (is_numeric($profile)) {
            $profileId = $profile;
        } else {
            return null;
        }
        return $this->getUrl('*/*/view', array('id' => $profileId));
    }

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}