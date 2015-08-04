<?php

class Spranks_AutomaticInvoices_Model_Observer
{

    /**
     * If the checkout has successfully been finished, directly
     * create an invoice and send it to the customer.
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutSubmitAllAfter(Varien_Event_Observer $observer)
    {
        // if module is disabled, do nothing
        if ( ! Mage::getStoreConfig('spranks_automaticinvoices/general/is_active')) {
            return;
        }
        // get the order from the onepage checkout or the orderS from the multishipping checkout
        $orders = $observer->getOrders();
        if (empty($orders)) {
            $orders = array($observer->getOrder());
            if (empty($orders)) {
                return;
            }
        }

        $helper = Mage::helper('spranks_automaticinvoices');
        foreach ($orders as $order) {
            /* @var $order Mage_Sales_Model_Order */
            // if orders with this payment method should not be invoiced, do nothing
            if ( ! $helper->shouldInvoiceOrder($order)) {
                continue;
            }
            // if order can be invoiced / has not been invoiced yet, invoice it
            if ($order->canInvoice()) {
                /* @var $invoice Mage_Sales_Model_Order_Invoice */
                $invoice = $order->prepareInvoice();
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
            if ( ! Mage::getStoreConfig('spranks_automaticinvoices/general/send_invoice_email')) {
                continue;
            }
            // if invoice has not been sent to the customer yet, send it now
            if ($invoice && ! $invoice->getEmailSent()) {
                $invoice->sendEmail();
            }
        }
    }

}
