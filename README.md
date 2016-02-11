# AramexShipping #


## Contents ##

* **Introduction**
* **Re-Write”DI”**
* **Installation**
* **Contribution**

## Introduction: ##

Aramex module was depvelopment as an online shipping carrier, so the base model will extend from **AbstractCarrierOnline** instead of **AbstractCarrier** which usually used for the fixed shipping rate modules,
In Aramex module we are working to gather all required information to ask Aramex providing us with shipping rate, the big challenge here was how to get all information from checkout page including Address fields”Country,Zipcode and City”,
Magento2 approach has changed a bit here, every field you need to get it you have to send it via javascript, Magento2 was designed to send Country_id and zip-code only, and that’s good for non- **MENA** region, but in **MENA** we should to include city name instead of zip-code and that was the first challenge with Magento2,

## Re-Write”DI”: ##
As we mentioned, the big challenge was how to get city field value from checkout page and the main steps:

**1- Read city field and submit it from JS.**

**2- Modify quote web API in order to get and set city.**

**3- Add city value to estimate shipping methods.** 
************************************
**1- Read city field and submit it from JS:**

To implement this step we should add city field as a required field with   shipping-rates-validation-rules.js and we should to rewrite all methods that read checkout page fields to add city value to them, so we should to rewrite **new-address.js** and **address-converter.js** and add city field to address array.


2- **Modify quote web API in order to get and set city:**

By this step we’ll add two methods ”set/get(city)” to **AramexEstimateAddressInterface** and implement them in **AramexEstimateAddress**

 
**3- Add city value to estimate shipping methods:**

By this step we should to Re-write all methods which JS call them to get estimate address shipping , so first of every thing we should to rewrite **WebApi** file to make all calls redirect to Aramex not core methods, then we’ll Re-write **ShippingMethodManagementInterface** with the  implementation file **ShippingMethodManagement**, 
the previous file was for registered clients so for guest will Re-write **GuestShippingMethodManagementInterface** with implementation file **GuestShippingMethodManagement**



## Installtion: ##
**1-** Clone the Aramex repository using either the HTTPS or SSH protocols.

**2-** Create a directory for the advanced acl module and copy the cloned repository contents to it:
   mkdir -p <your Magento install dir>/app/code/ShopGo/AramexShipping
    cp -R <AramexShipping clone dir>/* <your Magento install dir>/app/code/ShopGo/AramexShipping
* 

**3-** Run the following command:php <your Magento install dir>/bin/magento setup:upgrade

**4-**Make sure to remove static files cache using this command:
php <your Magento install dir>/bin/magento cache:flush


## Contribution ##
* Shopgo Team.
* info@shopgo.me