<?php

/**
 * PACKT News Model specialized for MySQL4
 *
 * @category   PACKT
 * @package    MST_News
 * @author     Nurul Ferdous <ferdous@dynamicguy.com>
 */
class MST_Menupro_Model_Mysql4_License extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        // Note that the news_id refers to the key field in your database table.
        $this->_init('menupro/license', 'license_id');
    }

}