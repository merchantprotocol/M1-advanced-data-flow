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

class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Payments extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $this->_initForm();
        return parent::_prepareForm();
    }

    protected function _initForm()
    {
        $item = $this->getProfile()->getSubscriptionItem();
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'payments',
            array('legend' => $this->__('Subscription Payments'))
        );

        $fieldset->addField(
            'currency',
            'label',
            array(
                 'name'  => 'currency',
                 'value' => $this->getProfile()->getData('details/currency_code'),
                 'label' => $this->__('Currency'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'billing_amount',
            'label',
            array(
                 'name'  => 'billing_amount',
                 'value' => $this->_formatPrice($this->getProfile()->getData('amount')),
                 'label' => $this->__('Billing Amount'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'shipping_amount',
            'label',
            array(
                 'name'  => 'shipping_amount',
                 'value' => $this->_formatPrice($this->getProfile()->getData('details/shipping_amount')),
                 'label' => $this->__('Shipping Amount'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'tax_amount',
            'label',
            array(

                 'name'  => 'tax_amount',
                 'value' => $this->_formatPrice($this->getProfile()->getData('details/tax_amount')),
                 'label' => $this->__('Tax Amount'),
                 'bold'  => true,
            )
        );
        if ($item->getTypeModel()->getTrialIsEnabled()) {
            $fieldset->addField(
                'trial_amount',
                'label',
                array(
                     'name'  => 'trial_amount',
                     'value' => $this->_formatPrice(
                         $this->getProfile()->getData('details/subscription/item/trial_price')
                     ),
                     'label' => $this->__('Trial Amount'),
                     'bold'  => true,
                )
            );
        }
        if ($item->getTypeModel()->getInitialFeeIsEnabled()) {
            $fieldset->addField(
                'initial_fee',
                'label',
                array(
                     'name'  => 'initial_fee',
                     'value' => $this->_formatPrice(
                         $this->getProfile()->getData('details/subscription/item/initial_fee_price')
                     ),
                     'label' => $this->__('Initial Fee'),
                     'bold'  => true,
                )
            );
        }

        $this->setForm($form);
    }

    public function getProfile()
    {
        return Mage::registry('current_profile');
    }

    protected function _formatPrice($price)
    {
        $currency = Mage::getModel('directory/currency')->load($this->getProfile()->getData('details/currency_code'));
        return $currency->format($price, array(), false);
    }
}