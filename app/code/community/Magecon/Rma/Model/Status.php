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
class Magecon_Rma_Model_Status extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('rma/status');
    }

    public function canDelete() {
        if ($this->getCode() != 'pending' && $this->getCode() != 'approved' && $this->getCode() != 'complete' && $this->getCode() != 'canceled') {
            return true;
        }
    }

    public function loadByCode($code) {
        foreach ($this->getCollection() as $model) {
            if ($model->getCode() == $code)
                return $model;
        }
    }

}