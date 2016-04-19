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

class AW_Sarp2_Block_Adminhtml_Subscription_Type_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_subscription_type';
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

        if (Mage::registry('current_type')->getAssociatedProfileCollection()->getSize() > 0) {
            $this->removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_type')->getId()) {
            return $this->__(
                'Edit Subscription Type "%s"', $this->escapeHtml(Mage::registry('current_type')->getTitle())
            );
        } else {
            return $this->__('Create Subscription Type');
        }
    }

    protected function _prepareLayout()
    {
        $tabsBlockJsObject = 'type_tabsJsTabs';
        $tabsBlockPrefix = 'type_tabs_';

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