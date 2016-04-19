<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
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