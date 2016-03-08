<?php

namespace ShopGo\AramexShipping\Block\System\Config\Button;

use Magento\Framework\App\Config\ScopeConfigInterface;
use ShopGo\AramexShipping\Helper\Data;

class CheckAccountButton extends \Magento\Config\Block\System\Config\Form\Field
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
        $this->setTemplate('system/config/check_account_button.phtml');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'reset_to_default_button',
                'label' => __('Run'),
                'onclick' => 'javascript:checkAramexAccount(); return false;',
            ]
        );

        return $button->toHtml();
    }


    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }


    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

     public function checkAccount()
    {
        return $this->_helper->checkAccount();
    }
}
