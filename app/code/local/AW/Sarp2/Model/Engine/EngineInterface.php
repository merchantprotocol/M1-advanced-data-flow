<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


interface AW_Sarp2_Model_Engine_EngineInterface
{
    /**
     * @return string
     */
    public function getEngineCode();

    /**
     * @return AW_Sarp2_Model_Engine_PaymentRestrictionsInterface
     */
    public function getPaymentRestrictionsModel();

    /**
     * @param AW_Sarp2_Model_Profile $p
     */
    public function createRecurringProfile(AW_Sarp2_Model_Profile $p);

    /**
     * @param AW_Sarp2_Model_Profile $p
     */
    public function updateRecurringProfile(AW_Sarp2_Model_Profile $p);

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     */
    public function updateStatusToActive(AW_Sarp2_Model_Profile $p, $note);

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     */
    public function updateStatusToSuspended(AW_Sarp2_Model_Profile $p, $note);

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param string                 $note
     */
    public function updateStatusToCanceled(AW_Sarp2_Model_Profile $p, $note);

    /**
     * @param AW_Sarp2_Model_Profile $p
     *
     * @return array
     */
    public function getRecurringProfileDetails(AW_Sarp2_Model_Profile $p);

    /**
     * @return AW_Sarp2_Model_Source_SourceInterface
     */
    public function getUnitSource();

    /**
     * @return AW_Sarp2_Model_Source_SourceInterface
     */
    public function getStatusSource();
}