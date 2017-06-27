<?php

class Spranks_AutomaticInvoices_Block_Adminhtml_System_Config_Form_Field_Select_CaptureType
    extends Mage_Core_Block_Html_Select
{

    public function _toHtml()
    {
        $options = Mage::getSingleton('spranks_automaticinvoices/adminhtml_system_config_source_invoice_captureType')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        $this->setExtraParams('style="width: 150px;"');

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

}
