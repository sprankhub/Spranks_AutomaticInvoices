<?php

class Spranks_AutomaticInvoices_Model_Adminhtml_System_Config_Source_Payment_Allowedmethods
{

    public function toOptionArray()
    {
        $payments       = Mage::getSingleton('payment/config')->getActiveMethods();
        $paymentMethods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle                 = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $paymentMethods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $paymentMethods;
    }

}
