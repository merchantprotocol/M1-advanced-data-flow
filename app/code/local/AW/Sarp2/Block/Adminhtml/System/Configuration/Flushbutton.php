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

class AW_Sarp2_Block_Adminhtml_System_Configuration_Flushbutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('delete')
            ->setLabel($this->__('Flush all Recurring profiles'))
            ->setStyle("width:280px;")
            ->setId('aw_sarp2_flush')
            ->toHtml();
        $html .= '<div class="aw_sarp2_message"></div>';
        $html .= <<<HTML
<script type="text/javascript">
var AW_SARP2_CONFIG = {
    flushActionUrl: '{$this->getUrl('aw_recurring_admin/adminhtml_profile/flush')}',
    msgConfirm: '{$this->__('Are you sure to flush ALL recurring profiles?\nThis action is unrecoverable.')}',
    msgSuccess: '{$this->__('All Recurring profiles successfully flushed')}',
    msgFailure: '{$this->__('Connection error')}'
};
new AWSarp2FlushButton();
</script>
HTML;

        return $html;
    }
}