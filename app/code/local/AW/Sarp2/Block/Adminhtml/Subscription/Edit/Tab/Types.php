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


class AW_Sarp2_Block_Adminhtml_Subscription_Edit_Tab_Types extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    const FIELD_NAME_PATTERN = 'subscription_type[{type_id}][{field_id}]';

    protected $_baseFieldsetId;
    protected $_fieldsetIds = array();

    public function getTabLabel()
    {
        return $this->__('Subscription Types');
    }

    public function getTabTitle()
    {
        return $this->__('Subscription Types');
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
        return parent::getFormHtml() . $this->getForm()->getCustomStyle() . $this->_getInitJs();
    }

    protected function _prepareForm()
    {
        $this->_initForm();
        return parent::_prepareForm();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setCustomStyle('<style type="text/css">.fieldset-hidden{display:none;}</style>');

        $fieldset = $form->addFieldset(
            'subscription_type_',
            array(
                 'class' => 'fieldset-hidden',
            )
        );

        $this->_baseFieldsetId = $fieldset->getId();

        $fieldConfiguration = $this->getFieldConfiguration();
        foreach ($fieldConfiguration as $filedId => $fieldData) {
            $fieldData['attributes']['name'] = $this->_getFieldName('hidden', $filedId);
            $fieldset->addField($fieldData['attributes']['name'], $fieldData['type'], $fieldData['attributes']);
        }

        if (Mage::registry('current_subscription')->getData('subscription_type')) {
            $typesData = Mage::registry('current_subscription')->getData('subscription_type');
        } else {
            $typesData = Mage::registry('current_subscription')->getItemCollection();
        }
        foreach ($typesData as $storedTypeId => $storedTypeData) {
            $fieldset = $form->addFieldset('subscription_type_' . $storedTypeId, array());
            $this->_fieldsetIds[] = $fieldset->getId();

            foreach ($fieldConfiguration as $filedId => $fieldData) {
                $fieldData['attributes']['name'] = $this->_getFieldName($storedTypeId, $filedId);
                if (isset($storedTypeData[$filedId])) {
                    $fieldData['attributes']['value'] = $storedTypeData[$filedId];
                }
                $fieldset->addField($fieldData['attributes']['name'], $fieldData['type'], $fieldData['attributes']);
            }
        }

        $this->setForm($form);
    }

    public function getSubscriptionTypesAsOptionArray()
    {
        return Mage::getResourceModel('aw_sarp2/subscription_type_collection')->toOptionArray();
    }

    public function getFieldConfiguration()
    {
        $fieldConfiguration = array(
            'subscription_type_id' => array(
                'type'       => 'select',
                'attributes' => array(
                    'class'    => 'subscription_type_id',
                    'name'     => 'subscription_type[{position_id}][{field_id}]',
                    'title'    => $this->__('Subscription Type'),
                    'label'    => $this->__('Subscription Type'),
                    'values'   => $this->getSubscriptionTypesAsOptionArray(),
                    'required' => 'true',
                ),
            ),
            'regular_price'        => array(
                'type'       => 'text',
                'attributes' => array(
                    'class'    => 'regular_price validate-number validate-not-negative-number',
                    'name'     => 'subscription_type[{position_id}][{field_id}]',
                    'title'    => $this->__('Price Per Iteration'),
                    'label'    => $this->__('Price Per Iteration'),
                    'required' => 'true',
                ),
            ),
            'trial_price'          => array(
                'type'       => 'text',
                'attributes' => array(
                    'class'    => 'trial_price validate-number validate-not-negative-number',
                    'name'     => 'subscription_type[{position_id}][{field_id}]',
                    'title'    => $this->__('Trial Period Price'),
                    'label'    => $this->__('Trial Period Price'),
                    'required' => 'true',
                ),
            ),
            'initial_fee_price'    => array(
                'type'       => 'text',
                'attributes' => array(
                    'class'    => 'initial_fee_price validate-number validate-greater-than-zero',
                    'name'     => 'subscription_type[{position_id}][{field_id}]',
                    'title'    => $this->__('Initial Fee'),
                    'label'    => $this->__('Initial Fee'),
                    'required' => 'true',
                ),
            ),
            'sort_order'           => array(
                'type'       => 'text',
                'attributes' => array(
                    'class'    => 'sort_order validate-number',
                    'name'     => 'subscription_type[{position_id}][{field_id}]',
                    'title'    => $this->__('Sort Order'),
                    'label'    => $this->__('Sort Order'),
                    'required' => 'true',
                ),
            ),
        );
        return $fieldConfiguration;
    }

    public function getTypeConfig()
    {
        $typeConfig = array();
        foreach (Mage::getResourceModel('aw_sarp2/subscription_type_collection') as $key => $type) {
            $typeConfig[$key] = array(
                'subscription_type_id' => true,
                'regular_price'        => true,
                'trial_price'          => !!$type->getTrialIsEnabled(),
                'initial_fee_price'    => !!$type->getInitialFeeIsEnabled(),
                'sort_order'           => true,
            );
        }
        return $typeConfig;
    }

    protected function _getFieldName($typeId, $filedId)
    {
        $search = array(
            '{type_id}',
            '{field_id}',
        );
        $replace = array(
            $typeId,
            $filedId,
        );
        return str_replace($search, $replace, self::FIELD_NAME_PATTERN);
    }

    protected function _getInitJs()
    {
        return
            '<script type="text/javascript">
                Event.observe(document, "dom:loaded", function(e){
                    var baseFieldsetId = "' . $this->_baseFieldsetId . '";
                    var fieldsetIds = ' . Zend_Json::encode($this->_fieldsetIds) . ';
                    var subscriptionTabId = "' . $this->getParentBlock()->getId() . '_' . $this->getTabId() . '";
                    var subscriptionTypeConfig = ' . Zend_Json::encode($this->getTypeConfig()) . ';
                    itemManager = new awTypeItemManager(
                        baseFieldsetId, fieldsetIds, subscriptionTypeConfig, subscriptionTabId
                    );
                });
            </script>'
        ;
    }
}