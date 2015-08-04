<?php

class Spranks_AutomaticInvoices_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Determines whether the given order should be invoiced.
     *
     * @param Mage_Sales_Model_Order $order the order to check
     *
     * @return bool
     */
    public function shouldInvoiceOrder(Mage_Sales_Model_Order $order)
    {
        // get a comma separated string of enabled payment methods
        $methodsToBeInvoiced = Mage::getStoreConfig('spranks_automaticinvoices/general/payment_methods');
        // put the enabled payment methods into an array
        $methodsToBeInvoicedArray = explode(',', $methodsToBeInvoiced);

        // check whether the given payment method is enabled in configuration
        $enabledPaymentMethod = in_array($order->getPayment()->getMethodInstance()->getCode(),
            $methodsToBeInvoicedArray);

        // dispatch an event so that more rules can be applied
        $result = new Varien_Object(array('should_invoice' => $enabledPaymentMethod));
        Mage::dispatchEvent('spranks_automaticinvoices_should_invoice_order',
            array('order' => $order, 'result' => $result));

        return $result->getShouldInvoice();
    }

}
