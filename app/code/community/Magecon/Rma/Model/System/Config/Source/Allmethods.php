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
class Magecon_Rma_Model_System_Config_Source_Allmethods {

    public function toOptionArray($isActiveOnlyFlag = false) {
        $methods = array(array('value' => '', 'label' => ''));
		// Change on 17.02.2014 by Hristo, ticket 410, get only active carries not all. 
		$carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool) $isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
            $methods[$carrierCode] = array(
                'label' => $carrierTitle,
                'value' => array(),
            );
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierTitle . ' - ' . $methodTitle,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                );
            }
        }

        return $methods;
    }

}