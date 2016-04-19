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

class AW_Sarp2_Block_Adminhtml_Widget_Grid_Column_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        $engineModel = Mage::helper('aw_sarp2/engine')->getEngineModelByCode($row->getSubscriptionEngineCode());
        if (is_null($engineModel)) {
            return null;
        }
        $options = $engineModel->getStatusSource()->toArray();
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (empty($options) && !is_array($options)) {
            return null;
        }
        $value = $row->getData($this->getColumn()->getIndex());
        if (is_array($value)) {
            $res = array();
            foreach ($value as $item) {
                if (isset($options[$item])) {
                    $res[] = $this->escapeHtml($options[$item]);
                } elseif ($showMissingOptionValues) {
                    $res[] = $this->escapeHtml($item);
                }
            }
            return implode(', ', $res);
        } elseif (isset($options[$value])) {
            return $this->escapeHtml($options[$value]);
        } elseif (in_array($value, $options)) {
            return $this->escapeHtml($value);
        }
    }
}