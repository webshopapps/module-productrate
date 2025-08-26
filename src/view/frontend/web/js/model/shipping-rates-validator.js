/*
 * WebShopApps ProductRate
 *
 * @category WebShopApps
 * @package WebShopApps\ProductRate
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.WebShopApps.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author WebShopApps Team sales@webshopapps.com
 *
 */
define(
    [
        'jquery',
        'mageUtils',
        './shipping-rates-validation-rules',
        'mage/translate'
    ],
    function ($, utils, validationRules, $t) {
        "use strict";
        return {
            validationErrors: [],
            validate: function (address) {
                var self = this;
                this.validationErrors = [];
                $.each(validationRules.getRules(), function (field, rule) {
                    if (rule.required && utils.isEmpty(address[field])) {
                        var message = $t('Field ') + field + $t(' is required.');
                        self.validationErrors.push(message);
                    }
                });
                return !Boolean(this.validationErrors.length);
            }
        };
    }
);
