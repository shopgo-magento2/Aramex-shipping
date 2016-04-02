define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'ShopGo_AramexShipping/js/model/shipping-rates-validator',
        'ShopGo_AramexShipping/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        aramexShippingRatesValidator,
        aramexShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('aramex', aramexShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('aramex', aramexShippingRatesValidationRules);
        return Component;
    }
);
