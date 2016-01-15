<?php

namespace Shopgo\AramexShipping\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_DEBUG  = 'carriers/aramex/debug';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

        )
    {    
        parent::__construct($context, $scopeConfig);
    }

    public function converCurrency($from, $to, $amount)
    {
        $url  = "https://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
        $data = file_get_contents($url);
        preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
        return round($converted);
    }

    public function getDebugStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
   }

}