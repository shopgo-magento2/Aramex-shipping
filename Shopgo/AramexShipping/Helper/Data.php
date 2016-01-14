<?php

namespace Shopgo\AramexShipping\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {    
        parent::__construct($context);
    }

    function converCurrency($from, $to, $amount)
    {
        $url  = "https://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
        $data = file_get_contents($url);
        preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
        return round($converted);
    }
}