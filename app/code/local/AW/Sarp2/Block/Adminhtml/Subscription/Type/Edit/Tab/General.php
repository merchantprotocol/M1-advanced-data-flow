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


class AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_General
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

    protected function _prepareForm()
    {
        $this->_initForm()->_setFormValues();
        return parent::_prepareForm();
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_General
     */
    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'subscription_type_general',
            array('legend' => $this->__('General'))
        );

        $fieldset->addField(
            'engine_code',
            'hidden',
            array(
                 'required' => false,
                 'name'     => 'engine_code',
            )
        );

        $fieldset->addField(
            'engine_code_label',
            'label',
            array(
                 'required' => false,
                 'name'     => 'engine_code_label',
                 'label'    => 'Engine',
            )
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                 'label'    => $this->__('Title'),
                 'title'    => $this->__('Title'),
                 'name'     => 'title',
                 'required' => true,
            )
        );

        $fieldset->addField(
            'is_visible',
            'select',
            array(
                 'title'  => $this->__('Status'),
                 'label'  => $this->__('Status'),
                 'name'   => 'is_visible',
                 'values' => Mage::getModel('aw_sarp2/source_subscription_type_visibility')->toOptionArray(),
            )
        );

        $fieldset->addField(
            'store_ids',
            'multiselect',
            array(
                 'name'   => 'store_ids[]',
                 'title'  => $this->__('Linked Store IDs'),
                 'label'  => $this->__('Linked Store IDs'),
                 'values' => $this->_getStoreValuesForForm(),
            )
        );
        $this->setForm($form);
        return $this;
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_General
     */
    protected function _setFormValues()
    {
        $form = $this->getForm();
        if (Mage::registry('current_type')) {
            $data = Mage::registry('current_type')->getData();
            $data['engine_code_label'] = Mage::helper('aw_sarp2/engine')->getEngineLabelByCode($data['engine_code']);
            $form->setValues($data);
        }
        return $this;
    }

    /** copypast from Mage_Adminhtml_Model_System_Store */
    private function _getStoreValuesForForm()
    {
        $eWebs = Mage::helper('aw_sarp2/engine')->getWebsitesByEngine(Mage::registry('current_type')->getEngineModel());
        foreach ($eWebs as $key => $value) {
            $eWebs[$key] = $value['website_id'];
        }
        $options = array();
        $options[] = array(
            'label' => Mage::helper('adminhtml')->__('All Store Views'),
            'value' => 0
        );
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach (Mage::getSingleton('adminhtml/system_store')->getWebsiteCollection() as $website) {
            if (!in_array($website->getId(), $eWebs)) {
                continue;
            }
            $websiteShow = false;
            foreach (Mage::getSingleton('adminhtml/system_store')->getGroupCollection() as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach (Mage::getSingleton('adminhtml/system_store')->getStoreCollection() as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = array(
                            'label' => $website->getName(),
                            'value' => array()
                        );
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values = array();
                    }
                    $values[] = array(
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
                        'value' => $store->getId(),
                    );
                }
                if ($groupShow) {
                    $options[] = array(
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
                        'value' => $values,
                    );
                }
            }
        }
        return $options;
    }
}