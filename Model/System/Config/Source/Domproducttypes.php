<?php

namespace ShopGo\AramexShipping\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Domproducttypes implements ArrayInterface
{
    const OND  = "OND";
    const ONP  = "ONP";
    const CDS  = "CDS";

    public function toOptionArray()
    {
        return [
            self::OND => __('Overnight Document'),
            self::ONP => __('Overnight Parcel'),
            self::CDS => __('Credit Cards Delivery')
        ];
    }
}