<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shopgo\AramexShipping\Model\Rewrite;

use Magento\Quote\Model\ShippingMethodManagement;
use Shopgo\AramexShipping\Api\Data\Rewrite\AramexEstimateAddressInterface;
use Shopgo\AramexShipping\Api\Data\Rewrite\AramexShippingMethodManagementInterface;

class AramexShippingMethodManagement extends ShippingMethodManagement implements AramexShippingMethodManagementInterface

{
    protected $city;

    /**
     * {@inheritDoc}
     */
    public function aramexEstimateByAddress($cartId, AramexEstimateAddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        $this->city = $address->getCity();
        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddressId($cartId, $addressId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->addressRepository->getById($addressId);

        $this->city = $address->getCity();
        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion()
        );
    }

    /**
     * Get estimated rates
     *
     * @param Quote $quote
     * @param int $country
     * @param string $postcode
     * @param int $regionId
     * @param string $region
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    protected function getEstimatedRates(\Magento\Quote\Model\Quote $quote, $country, $postcode, $regionId, $region)
    {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($country);
        $shippingAddress->setPostcode($postcode);
        $shippingAddress->setRegionId($regionId);
        $shippingAddress->setRegion($region);
        $shippingAddress->setCity($this->city);
        $shippingAddress->setCollectShippingRates(true);
        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }
        return $output;
    }
}
