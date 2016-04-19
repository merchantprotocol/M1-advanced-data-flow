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

class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Reference extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $this->_initForm();
        return parent::_prepareForm();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'reference',
            array('legend' => $this->__('Reference'))
        );

        $engineCode = $this->getProfile()->getData('subscription_engine_code');
        $paymentMethodCode = $this->getProfile()->getData('details/method_code');
        $engineLabel = Mage::helper('aw_sarp2/engine')->getEngineLabelByCode($engineCode);
        $paymentMethod = Mage::helper('payment')->getMethodInstance($paymentMethodCode);
        $paymentMethodLabel = $paymentMethod->getTitle();
        $fieldset->addField(
            'payment_method',
            'label',
            array(
                 'name'  => 'payment_method',
                 'value' => $this->__("[%s] %s", $engineLabel, $paymentMethodLabel),
                 'label' => $this->__('Payment Method'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'payment_reference_id',
            'label',
            array(
                 'name'  => 'payment_reference_id',
                 'value' => $this->getProfile()->getReferenceId(),
                 'label' => $this->__('Payment Reference ID'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'schedule_description',
            'label',
            array(
                 'name'  => 'schedule_description',
                 'value' => $this->getProfile()->getData('details/description'),
                 'label' => $this->__('Schedule Description'),
                 'bold'  => true,
            )
        );

        $statusLabel = $this->getProfile()->getStatusLabel();
        if (is_null($statusLabel)) {
            $statusLabel = $this->getProfile()->getStatus();
        }
        $fieldset->addField(
            'status',
            'label',
            array(
                 'name'  => 'status',
                 'value' => $statusLabel,
                 'label' => $this->__('Profile Status'),
                 'bold'  => true,
            )
        );

        $this->setForm($form);
    }

    public function getProfile()
    {
        return Mage::registry('current_profile');
    }
}