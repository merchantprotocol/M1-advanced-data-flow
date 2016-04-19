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

class AW_Sarp2_Model_Engine_Authorizenet_Payment_Silentpost
{
    /**
     * @var AW_Sarp2_Model_Profile
     */
    protected $_recurringProfile;

    /**
     * @param AW_Sarp2_Model_Profile $p
     * @param array                  $data
     */
    public function process(AW_Sarp2_Model_Profile $p, $data)
    {
        $this->_recurringProfile = $p;
        $price = $data['x_amount'];
        if (!$data['x_tax_exempt']) {
            $price -= $data['x_tax'];
        }
        $productItemInfo = new Varien_Object;
        $paymentNumber = $data['x_subscription_paynum'];
        $initialDetails = $this->_recurringProfile->getInitialDetails();
        if (
            $initialDetails['subscription']['type']['trial_is_enabled']
            && ($paymentNumber <= $initialDetails['subscription']['type']['trial_number_of_occurrences'])
        ) {
            $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_TRIAL);
        } elseif (
            $paymentNumber <= $initialDetails['subscription']['type']['period_number_of_occurrences']
            || $initialDetails['subscription']['type']['period_is_infinite']
        ) {
            $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_REGULAR);
        } else {
            $p->synchronizeWithEngine();
            exit;
        }
        $productItemInfo->setTaxAmount($data['x_tax']);
        $productItemInfo->setShippingAmount($initialDetails['shipping_amount']);
        $productItemInfo->setPrice($price - $initialDetails['shipping_amount']);

        $order = $this->_recurringProfile->createOrder($productItemInfo);

        $payment = $order->getPayment();
        $payment->setTransactionId($data['x_trans_id'])
            ->setPreparedMessage($data['x_response_reason_text'])
            ->setIsTransactionClosed(0);
        $order->save();
        $this->_recurringProfile->addOrderRelation($order->getId());
        $payment->registerCaptureNotification($order->getBaseGrandTotal());
        $order->save();

        $p->synchronizeWithEngine();

        // notify customer
        if ($invoice = $payment->getCreatedInvoice()) {
            $message = Mage::helper('paygate')->__('Notified customer about invoice #%s.', $invoice->getIncrementId());
            $order->sendNewOrderEmail()->addStatusHistoryComment($message)
                ->setIsCustomerNotified(true)
                ->save()
            ;
        }
    }
}