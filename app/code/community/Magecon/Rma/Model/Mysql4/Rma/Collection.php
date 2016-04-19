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
class Magecon_Rma_Model_Mysql4_Rma_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('rma/rma');
    }

    public function addAttributeToFilter($attribute, $condition = null) {
        $this->addFieldToFilter($this->_attributeToField($attribute), $condition);
        return $this;
    }

    protected function _attributeToField($attribute) {
        $field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof Mage_Eav_Model_Entity_Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            Mage::throwException(Mage::helper('rma')->__('Cannot determine the field name.'));
        }
        return $field;
    }

}