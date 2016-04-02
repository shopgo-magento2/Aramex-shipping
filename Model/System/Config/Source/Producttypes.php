<?php

namespace ShopGo\AramexShipping\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Producttypes implements ArrayInterface
{
    const PDE  = "PDX";
    const PCE  = "PPX";
    const PLE  = "PLX";
    const DDE  = "DDX";
    const DPE  = "DPX";
    const GDE  = "GDX";
    const GPE  = "GPX";
    const EDX  = "EDX";
    const EPX  = "EPX";

    public function toOptionArray()
    {
        return [
            self::PDE => __('Priority Document Express'),
            self::PCE => __('Priority Parcel Express'),
            self::PLE => __('Priority Letter Express'),
            self::DDE => __('Deferred Document Express'),
            self::DPE => __('Deferred Parcel Express'),
            self::GDE => __('Ground Document Express'),
            self::GPE => __('Ground Parcel Express'),
            self::EDX => __('Economy Document Express'),
            self::EPX => __('Economy Parcel Express')
        ];
    }
}