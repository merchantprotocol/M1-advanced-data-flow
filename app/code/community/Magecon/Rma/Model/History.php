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
class Magecon_Rma_Model_History extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('rma/history');
    }

    public function loadByRMAId($rmaId) {
        foreach ($this->getCollection() as $model) {
            if ($model->getRmaId() == $rmaId)
                return $model;
        }
    }

    public function setHistoryCommmentForPendingStatus($rmaId) {
        $status = Mage::getModel('rma/status')->loadByCode('pending');
        $data = array("rma_id" => $rmaId,
            "comment" => $status->getHistory(),
            "status" => $status->getStatus(),
            "notify" => '1',
            "visible" => (trim($status->getHistory()) != '') ? '1' : '0',
            "creation_date" => now());
        $this->setData($data);
    }

    public function setHistoryCommmentForCanceledStatus($rmaId) {
        $status = Mage::getModel('rma/status')->loadByCode('canceled_by_customer');
        $data = array("rma_id" => $rmaId,
            "comment" => $status->getHistory(),
            "status" => $status->getStatus(),
            "notify" => '1',
            "visible" => (trim($status->getHistory()) != '') ? '1' : '0',
            "creation_date" => now());
        $this->setData($data);
    }

}