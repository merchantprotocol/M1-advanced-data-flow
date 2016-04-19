<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_Rma
 * @version    1.0.0
 * @copyright  Copyright (c) 2013 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_Rma_Block_Adminhtml_Rma_Create_New_Tab_Renderer_SelectQty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $html = '<select name="' . ( $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId() ) . '" ' . $this->getColumn()->getValidateClass() . '>';
        $value = $row->getData($this->getColumn()->getIndex());
        $sku = $row->getData('sku');
        $order = Mage::getModel('sales/order')->load($row->getData('order_id'));

        foreach ($order->getAllItems() as $item) {
            if ($sku == $item->getSku())
                $qty = (int) $item->getQtyInvoiced();
        }
        for ($i = 0; $i <= $qty; $i++) {
            $selected = ( ($i == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html.= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
        }
        $html.='</select>';
        return $html;
    }

}