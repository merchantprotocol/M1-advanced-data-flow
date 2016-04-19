<?php

/**
 * PACKT News Model
 *
 * @category   PACKT
 * @package    MST_News
 * @author     Nurul Ferdous <ferdous@dynamicguy.com>
 */
class MST_Menupro_Model_License extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('menupro/license');
    }
    
}