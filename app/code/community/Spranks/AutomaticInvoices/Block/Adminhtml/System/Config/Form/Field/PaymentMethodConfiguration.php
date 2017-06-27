<?php

class Spranks_AutomaticInvoices_Block_Adminhtml_System_Config_Form_Field_PaymentMethodConfiguration
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_paymentMethodItemRenderer;

    protected $_sendInvoiceEmailRenderer;

    protected $_captureTypeRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('payment_method', array(
            'label'    => $this->__('Payment Method'),
            'renderer' => $this->_getPaymentMethodRenderer()
        ));
        $this->addColumn('send_invoice_email', array(
            'label'    => $this->__('Send invoice mail'),
            'renderer' => $this->_getSendInvoiceEmailRenderer()
        ));
        $this->addColumn('capture_invoice', array(
            'label'    => $this->__('Capture invoice'),
            'renderer' => $this->_getCaptureTypeRenderer()
        ));

        $this->_addAfter       = false;
        $this->_addButtonLabel = $this->__('Add');
    }

    protected function _getPaymentMethodRenderer()
    {
        if ( ! $this->_paymentMethodItemRenderer) {
            $this->_paymentMethodItemRenderer = $this->getLayout()->createBlock(
                'spranks_automaticinvoices/adminhtml_system_config_form_field_select_paymentMethod', '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_paymentMethodItemRenderer;
    }

    protected function _getSendInvoiceEmailRenderer()
    {
        if ( ! $this->_sendInvoiceEmailRenderer) {
            $this->_sendInvoiceEmailRenderer = $this->getLayout()->createBlock(
                'spranks_automaticinvoices/adminhtml_system_config_form_field_select_sendInvoiceEmail', '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_sendInvoiceEmailRenderer;
    }

    protected function _getCaptureTypeRenderer()
    {
        if ( ! $this->_captureTypeRenderer) {
            $this->_captureTypeRenderer = $this->getLayout()->createBlock(
                'spranks_automaticinvoices/adminhtml_system_config_form_field_select_captureType', '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_captureTypeRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getPaymentMethodRenderer()
                ->calcOptionHash($row->getData('payment_method')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getSendInvoiceEmailRenderer()
                ->calcOptionHash($row->getData('send_invoice_email')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getCaptureTypeRenderer()
                ->calcOptionHash($row->getData('capture_invoice')),
            'selected="selected"'
        );
    }

}
