<?php

class Spranks_AutomaticInvoices_Model_Adminhtml_System_Config_Source_Invoice_CaptureType
{

    public function toOptionArray()
    {
        $helper       = Mage::helper('spranks_automaticinvoices');
        $captureTypes = array(
            Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE  => array(
                'label' => $helper->__('Capture Online'),
                'value' => Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE
            ),
            Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE => array(
                'label' => $helper->__('Capture Offline'),
                'value' => Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE
            ),
            Mage_Sales_Model_Order_Invoice::NOT_CAPTURE     => array(
                'label' => $helper->__('Not Capture'),
                'value' => Mage_Sales_Model_Order_Invoice::NOT_CAPTURE
            ),
        );

        return $captureTypes;
    }

}
