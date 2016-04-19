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


class AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_Schedule extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('Schedule');
    }

    public function getTabTitle()
    {
        return $this->__('Schedule');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getFormHtml()
    {
        return parent::getFormHtml() . $this->_getInitJs();
    }

    protected function _prepareForm()
    {
        $this->_initForm()->_setFormValues();
        return parent::_prepareForm();
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_Schedule
     */
    protected function _initForm()
    {
        $engineModel = Mage::registry('current_type')->getEngineModel();
        $form = new Varien_Data_Form();

        $generalFieldset = $form->addFieldset(
            'subscription_type_schedule_general',
            array('legend' => $this->__('General'))
        );

        $generalFieldset->addField(
            'period_unit',
            'select',
            array(
                 'label'  => $this->__('Period Unit'),
                 'title'  => $this->__('Period Unit'),
                 'name'   => 'period_unit',
                 'values' => is_null($engineModel) ? array() : $engineModel->getUnitSource()->toOptionArray(),
            )
        );

        $generalFieldset->addField(
            'period_length',
            'text',
            array(
                 'label'    => $this->__('Number of Units in Period'),
                 'title'    => $this->__('Number of Units in Period'),
                 'name'     => 'period_length',
                 'class'    => 'validate-number',
                 'required' => true,
            )
        );

        $generalFieldset->addField(
            'period_is_infinite',
            'select',
            array(
                 'label'  => $this->__('Is Infinite'),
                 'title'  => $this->__('Is Infinite'),
                 'name'   => 'period_is_infinite',
                 'values' => Mage::getModel('aw_sarp2/source_yesno')->toOptionArray(),
            )
        );

        $generalFieldset->addField(
            'period_number_of_occurrences',
            'text',
            array(
                 'label'    => $this->__('Number of Occurrences'),
                 'title'    => $this->__('Number of Occurrences'),
                 'name'     => 'period_number_of_occurrences',
                 'class'    => 'validate-number',
                 'required' => true,
            )
        );

        $trialPeriodFieldset = $form->addFieldset(
            'subscription_type_schedule_trial_period',
            array('legend' => $this->__('Trial Period'))
        );

        if ($engineModel->getPaymentRestrictionsModel()->isTrialSupported()) {
            $trialPeriodFieldset->addField(
                'trial_is_enabled',
                'select',
                array(
                     'label'  => $this->__('Is Trial Period Enabled'),
                     'title'  => $this->__('Is Trial Period Enabled'),
                     'name'   => 'trial_is_enabled',
                     'values' => Mage::getModel('aw_sarp2/source_yesno')->toOptionArray(),
                )
            );

            $trialPeriodFieldset->addField(
                'trial_number_of_occurrences',
                'text',
                array(
                     'label'    => $this->__('Number Of Occurrences for Trial Period'),
                     'title'    => $this->__('Number Of Occurrences for Trial Period'),
                     'name'     => 'trial_number_of_occurrences',
                     'class'    => 'validate-number',
                     'required' => 'true',
                )
            );
        } else {
            $trialPeriodFieldset->addField(
                'trial_is_enabled',
                'hidden',
                array(
                     'name'  => 'trial_is_enabled',
                     'value' => 0,
                )
            );
            $trialPeriodFieldset->addField(
                'trial_is_enabled_label',
                'label',
                array(
                     'required' => false,
                     'name'     => 'trial_is_enabled_label',
                     'label'    => 'Is Trial Period Enabled',
                )
            );
        }

        $initialFeeFieldset = $form->addFieldset(
            'subscription_type_schedule_initial_fee',
            array('legend' => $this->__('Initial Fee'))
        );

        if ($engineModel->getPaymentRestrictionsModel()->isInitialAmountSupported()) {
            $initialFeeFieldset->addField(
                'initial_fee_is_enabled',
                'select',
                array(
                     'label'  => $this->__('Is Initial Fee Enabled'),
                     'title'  => $this->__('Is Initial Fee Enabled'),
                     'name'   => 'initial_fee_is_enabled',
                     'values' => Mage::getModel('aw_sarp2/source_yesno')->toOptionArray(),
                )
            );
        } else {
            $initialFeeFieldset->addField(
                'initial_fee_is_enabled',
                'hidden',
                array(
                     'name'  => 'initial_fee_is_enabled',
                     'value' => 0,
                )
            );
            $initialFeeFieldset->addField(
                'initial_fee_is_enabled_label',
                'label',
                array(
                     'required' => false,
                     'name'     => 'initial_fee_is_enabled_label',
                     'label'    => 'Is Initial Fee Enabled',
                )
            );
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_Schedule
     */
    protected function _setFormValues()
    {
        $form = $this->getForm();
        if (Mage::registry('current_type')) {
            $data = Mage::registry('current_type')->getData();
            $data['trial_is_enabled_label'] = $this->__('Engine not supported Trial Period');
            $data['initial_fee_is_enabled_label'] = $this->__('Engine does not support Initial Fee');
            $form->setValues($data);
        }
        return $this;
    }

    protected function _getInitJs()
    {
        return
            '<script type="text/javascript">
                Event.observe(document, "dom:loaded", function(e) {
                    new awFieldDependence({
                        message           : "' . $this->__('Subscription has infinite period') . '",
                        available         : [0],
                        mainFieldId       : "period_is_infinite",
                        dependenceFieldId : "period_number_of_occurrences"
                    });
                    new awFieldDependence({
                        message           : "' . $this->__('Trial period disabled') . '",
                        available         : [1],
                        mainFieldId       : "trial_is_enabled",
                        dependenceFieldId : "trial_number_of_occurrences"
                    });
                });
            </script>';
    }
}