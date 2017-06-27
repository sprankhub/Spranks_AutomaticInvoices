<?php

class Spranks_AutomaticInvoices_Block_Adminhtml_System_Config_Form_Field_Select_PaymentMethod
    extends Mage_Core_Block_Html_Select
{

    public function _toHtml()
    {
        $options = Mage::getSingleton('spranks_automaticinvoices/adminhtml_system_config_source_payment_allowedmethods')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

}
