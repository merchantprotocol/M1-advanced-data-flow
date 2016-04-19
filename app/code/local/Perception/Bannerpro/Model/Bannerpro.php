<?php

class Perception_Bannerpro_Model_Bannerpro extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bannerpro/bannerpro');
    }
}