<?php

namespace Shopgo\AramexShipping\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Domproducttypes implements ArrayInterface
{
    const OND  = "OND";
    const ONP  = "ONP";
    const CDS  = "CDS";

    public function toOptionArray()
    {
        return [
            self::OND => __('OND'),
            self::ONP => __('ONP'),
            self::CDS => __('CDS')
        ];
    }
}