<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Featuredproducts
 * @copyright  Copyright (c) 2008-2009 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

class Magestore_Bannerpro_Model_Entity_Attribute_Source_Boolean_Config extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
   /**
   * Retrive all attribute options
   *
   * @return array
   */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                	'label' => Mage::helper('bannerpro')->__('No'),
                    'value' =>  0
                ),
                array(
                    'label' => Mage::helper('bannerpro')->__('Yes'),
                    'value' =>  1
                )
            );
        }
        return $this->_options;
    }
}
