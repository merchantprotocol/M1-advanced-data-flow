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


class AW_Sarp2_Block_Adminhtml_Subscription_Type_New_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('Settings');
    }

    public function getTabTitle()
    {
        return $this->__('Settings');
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
        return parent::_prepareForm();
    }

    /**
     * @return AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit_Tab_General
     */
    protected function _initForm()
    {
        $_form = new Varien_Data_Form();

        $_fieldset = $_form->addFieldset(
            'general',
            array(
                 'legend' => $this->__('General')
            )
        );

        $websiteOptions = array();
        foreach (Mage::app()->getWebsites() as $item) {
            $websiteOptions[$item['website_id']] = $item['name'];
        }
        if (!Mage::app()->isSingleStoreMode()) {
            $_fieldset->addField(
                'website',
                'select',
                array(
                     'name'   => 'website',
                     'label'  => $this->__('Website'),
                     'values' => $websiteOptions,
                )
            );
        } else {
            $_fieldset->addField(
                'website',
                'hidden',
                array(
                     'name'  => 'website',
                     'value' => key($websiteOptions),
                )
            );
        }

        $_fieldset->addType('engine', 'AW_Sarp2_Block_Form_Element_Engine');

        $_fieldset->addField(
            'engine_code',
            'engine',
            array(
                 'label'                => $this->__('Subscription Engine'),
                 'label_id'             => 'engine_label',
                 'name'                 => 'engine_code',
                 'configure_link_title' => $this->__('Configure'),
                 'link'                 => $this->getConfigurationUrl(),
            )
        );

        $_fieldset->addField(
            'submit',
            'submit',
            array(
                 'name'  => 'submit',
                 'value' => $this->__('Continue'),
                 'class' => 'button form-button'
            )
        );

        $this->setForm($_form);
        return $this;
    }

    public function getConfigurationUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', array('section' => 'aw_sarp2'));
    }

    protected function _getInitJs()
    {
        $engineWebsiteConfig = Zend_Json::encode($this->getEngineWebsiteConfig());
        return <<<HTML
            <script type="text/javascript">
                var AW_SARP2_CONFIG = {
                    errorMessage: '{$this->__('Subscription Engine not specified')}'
                };
                Event.observe(document, "dom:loaded", function(e) {
                    var engineWebsiteConfig = {$engineWebsiteConfig};
                    var itemManager = new awDependenceItem(engineWebsiteConfig);
                });
            </script>
HTML;
    }

    public function getEngineWebsiteConfig()
    {
        return Mage::helper('aw_sarp2/engine')->getEngineWebsiteConfig();
    }
}