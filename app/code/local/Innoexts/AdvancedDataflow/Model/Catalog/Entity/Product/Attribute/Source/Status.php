<?php
/**
 * Merchant Protocol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Merchant Protocol Commercial License (MPCL 1.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://merchantprotocol.com/commercial-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@merchantprotocol.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.merchantprotocol.com for more information.
 *
 * @category   MP
 * @package    MP_AdvancedDataflow
 * @copyright  Copyright (c) 2006-2016 Merchant Protocol LLC. and affiliates (https://merchantprotocol.com/)
 * @license    https://merchantprotocol.com/commercial-license/  Merchant Protocol Commercial License (MPCL 1.0)
 */

/**
 * Status source
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Catalog_Entity_Product_Attribute_Source_Status 
    extends Innoexts_AdvancedDataflow_Model_Entity_Attribute_Source 
{
    /**
     * Get options
     * 
     * @return array
     */
    protected function _getOptions()
    {
        $array = Mage::getModel('catalog/product_status')->getOptionArray();
        $options = array();
        if (is_array($array) && count($array)) {
            foreach ($array as $value => $label) {
                array_push($options, array('value' => $value, 'label' => $label, ));
            }
        }
        return $options;
    }
}
