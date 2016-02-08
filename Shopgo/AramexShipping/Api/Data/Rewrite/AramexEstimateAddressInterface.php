<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shopgo\AramexShipping\Api\Data\Rewrite;

use  Magento\Framework\Api\CustomAttributesDataInterface;


/**
 * Interface EstimateAddressInterface
 * @api
 */
interface AramexEstimateAddressInterface extends CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
//    const KEY_COUNTRY_ID = 'country_id';
//
//    const KEY_REGION_ID = 'region_id';
//
//    const KEY_REGION = 'region';
//
//    const KEY_POSTCODE = 'postcode';

    const KEY_CITY = 'city';

    /**#@-*/

    /**
     * Get region name
     *
     * @return string
     */
    public function getRegion();

    /**
     * Set region name
     *
     * @param string $region
     * @return $this
     */
    public function setRegion($region);

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId();

    /**
     * Set region id
     *
     * @param int $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Get country id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Set country id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);


    /**
     * Get city
     *
     * @return string
     */
    public function getCity();//emad

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */

    public function setCity($city);//emad


    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Quote\Api\Data\EstimateAddressExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Quote\Api\Data\EstimateAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Quote\Api\Data\EstimateAddressExtensionInterface $extensionAttributes
    );
}
