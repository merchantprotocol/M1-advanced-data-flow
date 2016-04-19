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

class AW_Sarp2_Block_Adminhtml_Subscription_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_subscription';
        $this->_blockGroup = 'aw_sarp2';
        parent::__construct();

        $this->_addButton(
            'saveandcontinueedit',
            array(
                 'label'   => $this->__('Save and Continue Edit'),
                 'onclick' => "saveAndContinueEdit('{$this->getSaveAndContinueUrl()}')",
                 'class'   => 'save',
                 'id'      => 'aw_sarp2-save-and-continue',
            ), -200
        );
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_subscription')->getId()) {
            return $this->__(
                'Edit Subscription for product "%s"', $this->escapeHtml(Mage::registry('current_product')->getName())
            );
        } else {
            return $this->__(
                'Create Subscription for product "%s"', $this->escapeHtml(Mage::registry('current_product')->getName())
            );
        }
    }

    protected function _prepareLayout()
    {
        $tabsBlockJsObject = 'subscription_tabsJsTabs';
        $tabsBlockPrefix = 'subscription_tabs_';

        $this->_formScripts[] = "
            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = {$tabsBlockJsObject}.activeTab.id;
                var tabsBlockPrefix = '{$tabsBlockPrefix}';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            array(
                 '_current'   => true,
                 'back'       => 'edit',
                 'active_tab' => '{{tab_id}}',
            )
        );
    }
}