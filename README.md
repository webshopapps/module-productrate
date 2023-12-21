# ShipperHQ and WebShopApps ProductRate

A Shipping Rate module for Magento 2.x which gives you the ability to set your shipping rates on a product-by-product basis.

Facts
-----
- [extension on GitHub](https://github.com/webshopapps/module-productrate)

Description
-----------
The ProductRate shipping extension is the original solution for Magento that enables you to offer product specific shipping rates to customers. Enter shipping rates for each of your products in the admin panel and Product Rate will calculate the total shipping charge when your customers go to checkout.

This extension also gives you control of how shipping is calculated on multiple items. For example, you can add a flat or percentage surcharge on the shipping fee when there is more than one of the same product in the cart.

Compatibility
-------------
- Magento >= 2.3

per the [official Magento 2 requirements](https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/system-requirements.html)

Supports both Magento Opensource (Community) and Magento Commerce (Enterprise)

Compatibility with earlier editions is possible but not maintained.

Installation Instructions
-------------------------
Install using composer by adding to your composer file using commands:

1. composer require webshopapps/module-productrate
2. composer update
3. php bin/magento setup:upgrade

You must reindex your Magento store after installation
4. php bin/magento indexer:reindex

Configuration
-------
WebShopApps Product Rate allows you to set shipping prices per product in your catalog. 

Enable WebShopApps ProductRate
1. Login to your Magento admin 
2. Navigate to Stores > Configuration > Sales > Shipping Methods 
3. Open the WebShopApps Product Rate section, set Enabled to Yes 
4. Save Config 

Set Shipping Prices on your Products
1. Navigate to Catalog > Products 
2. Edit your product
3. Open Shipping section
4. Set a price for shipping for this item in the Shipping Fee field
5. Save 


To Test Your Configuration
1. Add your product to the cart
2. Navigate to the cart and estimate shipping rates 
3. Shipping rate will be the shipping fee you assigned to the item above


For further information on using ProductRate, please refer to our [online documentation](https://docs.shipperhq.com/category/webshopapps-extensions/product-rate/).
If you have any issues with this extension, open an issue on [GitHub](https://github.com/webshopapps/module-productrate/issues).

Credits
---------
The composer structure is taken from various sources, most heavily using structure from <https://github.com/sjparkinson/static-review>.

Assistance around composer, Magento2 structure, etc was also taken from these sources:

* <https://github.com/Genmato/MultiStoreSearchFields>
* <https://alankent.wordpress.com/2014/08/03/creating-a-magento-2-composer-module/>
* <https://github.com/SchumacherFM/mage2-advanced-rest-permissions>

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/webshopapps/module-productrate/issues).
Alternatively you can contact us via email at support@webshopapps.com

WebShopApps ProductRate is provided AS IS and we are not accepting feature requests at this time. Extended functionality is available via [ShipperHQ](https://www.shipperhq.com).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

License
-------
Copyright (c) 2015 Zowta LLC & Zowta Ltd. See [LICENSE] for details.

We also dutifully respect the [Magento] OSL license, which is included in this codebase.


[license]: LICENSE.txt
[magento]: https://github.com/magento/magento2/blob/2.4-develop/LICENSE.txt

Copyright
---------
Copyright (c) 2015 Zowta LLC & Zowta Ltd.
