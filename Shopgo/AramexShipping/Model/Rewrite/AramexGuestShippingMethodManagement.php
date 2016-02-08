<?php

namespace Shopgo\AramexShipping\Model\Rewrite;

use Magento\Quote\Api\ShippingMethodManagementInterface;
use Shopgo\AramexShipping\Api\Data\Rewrite\AramexShippingMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Shopgo\AramexShipping\Api\Data\Rewrite\AramexEstimateAddressInterface;
use Shopgo\AramexShipping\Api\Data\Rewrite\AramexGuestShippingMethodManagementInterface;
use Magento\Quote\Model\GuestCart\GuestShippingMethodManagement;

/**
 * Shipping method management class for guest carts.
 */
class AramexGuestShippingMethodManagement extends GuestShippingMethodManagement implements AramexGuestShippingMethodManagementInterface
{
    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * Constructs a shipping method read service object.
     *
     * @param AramexShippingMethodManagementInterface $shippingMethodManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        AramexShippingMethodManagementInterface $shippingMethodManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingMethodManagement->get($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function getList($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingMethodManagement->getList($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function set($cartId, $carrierCode, $methodCode)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingMethodManagement->set($quoteIdMask->getQuoteId(), $carrierCode, $methodCode);
    }

//    /**
//     * {@inheritDoc}
//     */
//    public function estimateByAddress($cartId, AramexEstimateAddressInterface $address)
//    {
//        /** @var $quoteIdMask QuoteIdMask */
//        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
//        return $this->shippingMethodManagement->estimateByAddress($quoteIdMask->getQuoteId(), $address);
//    }

    /**
     * {@inheritDoc}
     */
    public function aramexEstimateByAddress($cartId, AramexEstimateAddressInterface $address)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingMethodManagement->aramexEstimateByAddress($quoteIdMask->getQuoteId(), $address);
    }
}
