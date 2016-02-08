<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shopgo\AramexShipping\Api\Data\Rewrite;

/**
 * Shipping method management interface for guest carts.
 * @api
 */
interface AramexGuestShippingMethodManagementInterface
{
    /**
     * List applicable shipping methods for a specified quote.
     *
     * @param string $cartId The shopping cart ID.
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     * @throws \Magento\Framework\Exception\StateException The shipping address is not set.
     */
    public function getList($cartId);

    /**
     * Estimate shipping
     *
     * @param string $cartId The shopping cart ID.
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address The estimate address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
   // public function estimateByAddress($cartId, \Shopgo\AramexShipping\Api\Data\Rewrite\AramexEstimateAddressInterface $address);//\Magento\Quote\Api\Data\EstimateAddressInterface $address

    /**
     * Estimate shipping
     *
     * @param string $cartId The shopping cart ID.
     * @param AramexEstimateAddressInterface $address The estimate address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    public function aramexEstimateByAddress($cartId,  AramexEstimateAddressInterface $address);

}