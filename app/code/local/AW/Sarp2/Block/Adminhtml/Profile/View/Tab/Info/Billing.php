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

class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Billing extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        return parent::_prepareForm();
    }

    public function getBillingAddress()
    {
        $addressData = Mage::registry('current_profile')->getData('details/billing_address');
        if (!is_null($addressData)) {
            $address = Mage::getModel('customer/address')->setData($addressData);
            return $address;
        }
        return null;
    }
}