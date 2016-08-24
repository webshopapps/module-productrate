/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function() {
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
