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

class AW_Sarp2_Block_Adminhtml_Profile_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_profile';
        $this->_blockGroup = 'aw_sarp2';
        $this->_mode = 'view';
        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');

        if ($this->_isAllowedAction('cancel') && Mage::registry('current_profile')->canCancel()) {
            $message = $this->__('Are you sure you want to cancel this subscription?');
            $this->_addButton(
                'subscription_cancel',
                array(
                     'label'   => $this->__('Cancel'),
                     'onclick' => "deleteConfirm('{$message}', '{$this->getCancelUrl()}')",
                )
            );
        }

        if ($this->_isAllowedAction('suspend') && Mage::registry('current_profile')->canSuspend()) {
            $message = $this->__('Are you sure you want to suspend this subscription?');
            $this->_addButton(
                'subscription_suspend',
                array(
                     'label'   => $this->__('Suspend'),
                     'onclick' => "deleteConfirm('{$message}', '{$this->getSuspendUrl()}')",
                )
            );
        }

        if ($this->_isAllowedAction('activate') && Mage::registry('current_profile')->canActivate()) {
            $message = $this->__('Are you sure you want to activate this subscription?');
            $this->_addButton(
                'subscription_activate',
                array(
                     'label'   => $this->__('Activate'),
                     'onclick' => "deleteConfirm('{$message}', '{$this->getActivateUrl()}')",
                )
            );
        }

        if ($this->_isAllowedAction('update')) {
            $message = $this->__('Are you sure you want to update this subscription?');
            $this->_addButton(
                'subscription_update',
                array(
                     'label'   => $this->__('Get Update'),
                     'onclick' => "deleteConfirm('{$message}', '{$this->getUpdateUrl()}')",
                )
            );
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_profile')) {
            return $this->__(
                'View Profile "%s"', $this->escapeHtml(Mage::registry('current_profile')->getReferenceId())
            );
        } else {
            return $this->__('View Profile');
        }
    }

    protected function _isAllowedAction($action)
    {
        return true;
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function getSuspendUrl()
    {
        return $this->getUrl('*/*/suspend', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function getActivateUrl()
    {
        return $this->getUrl('*/*/activate', array('id' => Mage::registry('current_profile')->getId()));
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/update', array('id' => Mage::registry('current_profile')->getId()));
    }
}