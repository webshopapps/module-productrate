/*
 * WebShopApps ProductRate
 *
 * @category WebShopApps
 * @package WebShopApps_ProductRate
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.WebShopApps.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author WebShopApps Team sales@webshopapps.com
 *
 */

/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../model/shipping-rates-validator',
        '../model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        productrateShippingRatesValidator,
        productrateShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('productrate', productrateShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('productrate', productrateShippingRatesValidationRules);
        return Component;
    }
);
