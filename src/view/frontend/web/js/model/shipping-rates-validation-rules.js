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
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function () {
                return {
                    'postcode': {
                        'required': false
                    },
                    'country_id': {
                        'required': true
                    },
                    'region_id' : {
                        'required': false
                    },
                    'city' : {
                        'required': false
                    }
                };
            }
        };
    }
);
