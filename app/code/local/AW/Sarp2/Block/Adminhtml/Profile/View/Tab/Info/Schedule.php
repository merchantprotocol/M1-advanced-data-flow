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


class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Schedule extends Mage_Adminhtml_Block_Widget_Form
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
            'schedule',
            array('legend' => $this->__('Profile Schedule'))
        );

        $date = $this->getProfile()->getData('start_date');
        $fieldset->addField(
            'start_date',
            'label',
            array(
                 'name'  => 'start_date',
                 'value' => $this->_formatDate($date),
                 'label' => $this->__('Start Date'),
                 'bold'  => true,
            )
        );

        $date = $this->getProfile()->getData('details/next_billing_date');
        if (!is_null($date)) {
            //if no data then no display
            $fieldset->addField(
                'next_billing_date',
                'label',
                array(
                     'name'  => 'next_billing_date',
                     'value' => $this->_formatDate($date),
                     'label' => $this->__('Next Billing Date'),
                     'bold'  => true,
                )
            );
        }

        $date = $this->getProfile()->getData('details/final_payments_date');
        if (!is_null($date)) {
            //if no data then no display
            $fieldset->addField(
                'final_billing_date',
                'label',
                array(
                     'name'  => 'final_billing_date',
                     'value' => $this->_formatDate($date),
                     'label' => $this->__('Final Billing Date'),
                     'bold'  => true,
                )
            );
        }

        $fieldset->addField(
            'trial_period',
            'label',
            array(
                 'name'  => 'trial_period',
                 'value' => $this->_getTrialInfo(),
                 'label' => $this->__('Trial Period'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'billing_period',
            'label',
            array(
                 'name'  => 'billing_period',
                 'value' => $this->_getRegularInfo(),
                 'label' => $this->__('Billing Period'),
                 'bold'  => true,
            )
        );

        $this->setForm($form);
    }

    public function getProfile()
    {
        return Mage::registry('current_profile');
    }

    public function getSubscriptionItem()
    {
        return $this->getProfile()->getSubscriptionItem();
    }

    protected function _formatDate($date)
    {
        return Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
    }

    protected function _getTrialInfo()
    {
        $value = "";
        if (!is_null($this->getSubscriptionItem())) {
            $data = Mage::helper('aw_sarp2/humanizer')->getTrialPeriodInformation($this->getSubscriptionItem());
            if (array_key_exists('period', $data)) {
                $value .= $data['period'];
            }
            if (array_key_exists('occurrences', $data)) {
                $value .= "\n" . $data['occurrences'];
            }
        }
        if (strlen($value) === 0) {
            $value = $this->__('No trial period');
        }
        return $value;
    }

    protected function _getRegularInfo()
    {
        $value = "";
        if (!is_null($this->getSubscriptionItem())) {
            $data = Mage::helper('aw_sarp2/humanizer')->getRegularPeriodInformation($this->getSubscriptionItem());
            $value = $data['period'] . "\n" . $data['occurrences'];
        }
        return $value;
    }
}