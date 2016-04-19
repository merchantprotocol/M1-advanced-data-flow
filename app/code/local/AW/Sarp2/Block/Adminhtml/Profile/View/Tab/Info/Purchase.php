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

class AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Purchase extends Mage_Adminhtml_Block_Widget_Form
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
            'purchase',
            array('legend' => $this->__('Purchase Item'))
        );

        $fieldset->addField(
            'product_name',
            'label',
            array(
                 'name'  => 'product_name',
                 'value' => $this->getItem()->getName(),
                 'label' => $this->__('Product Name'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'sku',
            'label',
            array(
                 'name'  => 'sku',
                 'value' => $this->getItem()->getSku(),
                 'label' => $this->__('SKU'),
                 'bold'  => true,
            )
        );

        $fieldset->addField(
            'qty',
            'label',
            array(
                 'name'  => 'qty',
                 'value' => $this->getItem()->getQty(),
                 'label' => $this->__('Quantity'),
                 'bold'  => true,
            )
        );

        $this->setForm($form);
    }

    public function getItem()
    {
        $profile = Mage::registry('current_profile');
        $item = new Varien_Object();
        $item->setData($profile->getData('details/order_item_info'));
        return $item;
    }
}