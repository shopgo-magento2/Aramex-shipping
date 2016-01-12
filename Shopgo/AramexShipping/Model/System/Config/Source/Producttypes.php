<?php

namespace Shopgo\AramexShipping\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Producttypes implements ArrayInterface
{
    const PDE  = "Priority Document Express";
    const PCE  = "Priority Parcel Express";
    const PLE  = "Priority Letter Express";
    const DDE  = "Deferred Document Express";
    const DPE  = "Deferred Parcel Express";
    const GDE  = "Ground Document Express";
    const GPE  = "Ground Parcel Express";
    const EDX  = "Economy Document Express";
    const EPX  = "Economy Parcel Express";

    /**
     * Get positions of lastest news block
     *
     * @return array
     */
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