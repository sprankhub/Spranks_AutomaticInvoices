<?php

class Spranks_AutomaticInvoices_Helper_Config extends Mage_Core_Helper_Abstract
{

    const XML_PATH_IS_ACTIVE = 'spranks_automaticinvoices/general/is_active';

    const XML_PATH_PAYMENT_METHOD_CONFIG = 'spranks_automaticinvoices/general/payment_method_configuration';

    protected $_paymentMethodConfig = null;

    public function isActive($store = null)
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_IS_ACTIVE, $store);
    }

    protected function _getPaymentMethodConfig($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (is_null($this->_paymentMethodConfig) || ! isset($this->_paymentMethodConfig[$storeId])
            || is_null($this->_paymentMethodConfig[$storeId])
        ) {
            $storePaymentMethodConfig = array();
            $paymentMethodConfigs     = Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHOD_CONFIG, $store);
            if ($paymentMethodConfigs) {
                $paymentMethodConfigs = unserialize($paymentMethodConfigs);
                if (is_array($paymentMethodConfigs)) {
                    foreach ($paymentMethodConfigs as $paymentMethodConfig) {
                        $paymentMethod                            = $paymentMethodConfig['payment_method'];
                        $sendInvoiceEmail                         = $paymentMethodConfig['send_invoice_email'];
                        $captureInvoice                           = $paymentMethodConfig['capture_invoice'];
                        $storePaymentMethodConfig[$paymentMethod] = array(
                            'send_invoice_email' => $sendInvoiceEmail,
                            'capture_invoice'    => $captureInvoice
                        );
                    }
                }
            }
            $this->_paymentMethodConfig[$storeId] = $storePaymentMethodConfig;
        }

        return $this->_paymentMethodConfig[$storeId];
    }

    /**
     * Determines whether the given order should be invoiced.
     *
     * @param Mage_Sales_Model_Order $order the order to check
     *
     * @return bool
     */
    public function shouldInvoiceOrder(Mage_Sales_Model_Order $order)
    {
        $paymentMethodConfig = $this->_getPaymentMethodConfig($order->getStore());
        $methodsToBeInvoiced = array_keys($paymentMethodConfig);

        // check whether the given payment method is enabled in configuration
        $enabledPaymentMethod = in_array($order->getPayment()->getMethodInstance()->getCode(), $methodsToBeInvoiced);

        // dispatch an event so that more rules can be applied
        $result = new Varien_Object(array('should_invoice' => $enabledPaymentMethod));
        Mage::dispatchEvent('spranks_automaticinvoices_should_invoice_order',
            array('order' => $order, 'result' => $result));

        return $result->getData('should_invoice');
    }

    /**
     * Determines whether an invoice email should be sent for the given order.
     *
     * @param Mage_Sales_Model_Order $order the order to check
     *
     * @return bool
     */
    public function shouldSendInvoiceEmail(Mage_Sales_Model_Order $order)
    {
        $paymentMethodConfig    = $this->_getPaymentMethodConfig($order->getStore());
        $paymentMethodCode      = $order->getPayment()->getMethodInstance()->getCode();
        $shouldSendInvoiceEmail = false;
        if (isset($paymentMethodConfig[$paymentMethodCode])
            && isset($paymentMethodConfig[$paymentMethodCode]['send_invoice_email'])
            && $paymentMethodConfig[$paymentMethodCode]['send_invoice_email']
        ) {
            $shouldSendInvoiceEmail = true;
        }

        // dispatch an event so that more rules can be applied
        $result = new Varien_Object(array('should_send_invoice_email' => $shouldSendInvoiceEmail));
        Mage::dispatchEvent('spranks_automaticinvoices_should_send_invoice_email',
            array('order' => $order, 'result' => $result));

        return $result->getData('should_send_invoice_email');
    }

    /**
     * Determines whether and how the invoice should be captured for the given order.
     *
     * @param Mage_Sales_Model_Order $order the order to check
     *
     * @return bool
     */
    public function getCaptureInvoice(Mage_Sales_Model_Order $order)
    {
        $paymentMethodConfig = $this->_getPaymentMethodConfig($order->getStore());
        $paymentMethodCode   = $order->getPayment()->getMethodInstance()->getCode();
        $captureInvoice      = Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE;
        if (isset($paymentMethodConfig[$paymentMethodCode])
            && isset($paymentMethodConfig[$paymentMethodCode]['capture_invoice'])
        ) {
            $captureInvoice = $paymentMethodConfig[$paymentMethodCode]['capture_invoice'];
        }

        // dispatch an event so that more rules can be applied
        $result = new Varien_Object(array('capture_invoice' => $captureInvoice));
        Mage::dispatchEvent('spranks_automaticinvoices_capture_invoice',
            array('order' => $order, 'result' => $result));

        return $result->getData('capture_invoice');
    }

}
