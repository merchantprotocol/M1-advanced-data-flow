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
 * Order address country source model
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Sales_Order_Address_Attribute_Source_Country extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $collection = Mage::getResourceModel('directory/country_collection');
            $options = array();
            foreach ($collection as $country) {
                $label = Mage::app()->getLocale()->getCountryTranslation($country->getData('country_id'));
                $options[$label] = array(
                    'value'     => $country->getData('country_id'), 
                    'value2'    => $country->getData('iso3_code'), 
                    'label'     => $label
                );
            }
            ksort($options);
            $options = array_values($options);
            $this->_options = $options;
        }
        return $this->_options;
    }
}