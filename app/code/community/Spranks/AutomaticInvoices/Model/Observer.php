<?php

class Spranks_AutomaticInvoices_Model_Observer
{

    /**
     * If the checkout has successfully been finished, directly
     * create an invoice and send it to the customer.
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesModelServiceQuoteSubmitAfter(Varien_Event_Observer $observer)
    {
        /** @var Spranks_AutomaticInvoices_Helper_Config $helper */
        $helper = Mage::helper('spranks_automaticinvoices/config');
        // if module is disabled, do nothing
        if (!$helper->isActive()) {
            return;
        }
        $order = $observer->getOrder();
        if (empty($order)) {
            return;
        }

        /* @var $order Mage_Sales_Model_Order */
        // if orders with this payment method should not be invoiced, do nothing
        if (!$helper->shouldInvoiceOrder($order)) {
            return;
        }
        // if order can be invoiced / has not been invoiced yet, invoice it
        if ($order->canInvoice()) {
            $captureCase = $helper->getCaptureInvoice($order);
            /* @var $invoice Mage_Sales_Model_Order_Invoice */
            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase($captureCase);
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } else {
            // order has already been invoiced, so get the invoice
            $invoice = $order->getInvoiceCollection()->getFirstItem();
        }
        // if invoice mail should not be sent, do nothing more
        if (!$helper->shouldSendInvoiceEmail($order)) {
            return;
        }
        // if invoice has not been sent to the customer yet, send it now
        if ($invoice && !$invoice->getEmailSent()) {
            $invoice->sendEmail();
        }
    }

}
