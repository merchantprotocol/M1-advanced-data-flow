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


class AW_Sarp2_Block_Adminhtml_Subscription_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('General');
    }

    public function getTabTitle()
    {
        return $this->__('General');
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
        $this->_initForm();
        $this->_setFormValues();
        return parent::_prepareForm();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'subscription_type_general',
            array('legend' => $this->__('General'))
        );

        $fieldset->addField(
            'product_id',
            'hidden',
            array(
                 'required' => false,
                 'name'     => 'product_id',
            )
        );

        $fieldset->addType('product', 'AW_Sarp2_Block_Form_Element_Product');

        $fieldset->addField(
            'product_name',
            'product',
            array(
                 'label' => $this->__('Product Name'),
                 'name'  => 'product_name',
                 'href'  => Mage::getModel('adminhtml/url')->getUrl(
                     'adminhtml/catalog_product/edit',
                     array('id' => (int)Mage::registry('current_product')->getId())
                 ),
                 'value' => Mage::registry('current_product')->getName(),
            )
        );

        $fieldset->addField(
            'is_subscription_only',
            'select',
            array(
                 'label'  => $this->__('Is Subscription Only'),
                 'name'   => 'is_subscription_only',
                 'values' => Mage::getModel('aw_sarp2/source_yesno')->toOptionArray(),
            )
        );

        $customerGroups = array_merge(
            array($this->__('Not Selected')),
            Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash()
        );

        $fieldset->addField(
            'move_customer_to_group_id',
            'select',
            array(
                 'label'  => $this->__('Move Customer To Group'),
                 'name'   => 'move_customer_to_group_id',
                 'values' => $customerGroups,
            )
        );

        $fieldset->addField(
            'start_date_code',
            'select',
            array(
                 'label'  => $this->__('Start Date'),
                 'name'   => 'start_date_code',
                 'values' => Mage::getModel('aw_sarp2/source_subscription_startdate')->toArray(),
            )
        );

        $fieldset->addField(
            'day_of_month',
            'text',
            array(
                 'label'    => $this->__('Day of Month'),
                 'name'     => 'day_of_month',
                 'class'    => 'validate-number',
                 'required' => true,
            )
        );

        $this->setForm($form);
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Edit_Tab_General
     */
    protected function _setFormValues()
    {
        $form = $this->getForm();
        if (Mage::registry('current_subscription')) {
            $form->addValues(Mage::registry('current_subscription')->getData());
        }
        return $this;
    }

    protected function _getInitJs()
    {
        return
            '<script type="text/javascript">
                Event.observe(document, "dom:loaded", function(e) {
                    var startDayDependence = new awFieldDependence({
                        message           : "' . $this->__('Not available for Current <b>Start Date</b> type') . '",
                        available         : [4],
                        mainFieldId       : "start_date_code",
                        dependenceFieldId : "day_of_month"
                    });
                });
            </script>';
    }
}