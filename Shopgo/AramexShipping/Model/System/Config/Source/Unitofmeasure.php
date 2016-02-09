<?php

namespace Shopgo\AramexShipping\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Unitofmeasure implements ArrayInterface
{
    const KG  = "KG";
    const LB  = "LB";
    public function toOptionArray()
    {
        return [
            self::KG => __('KGs'),
            self::LB => __('LBs')
        ];
    }
}