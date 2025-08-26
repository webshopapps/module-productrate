# ShipperHQ ProductRate for Magento 2
The ProductRate shipping extension is the original Magento solution developed by [ShipperHQ](https://shipperhq.com) that enables you to charge per‑product shipping fees. Set a shipping fee on each product in your catalog and ProductRate will total the charges at checkout.

For businesses seeking even greater shipping customization, real‑time carrier rates, multi‑origin shipping, delivery dates, and more, consider upgrading to [ShipperHQ](https://shipperhq.com).

---

## Features

- **Per‑Product Shipping Fees**: Define a specific shipping charge on each product.
- **Quantity Surcharges**: Optionally add a flat or percentage surcharge when multiple units of the same product are in the cart.
- **Simple Admin Workflow**: Configure directly on the product—no CSVs required.
- **Works Alongside Other Methods**: Can run alongside other carrier rates; For advanced capabilities such as dimensional shipping and time‑in‑transit, check out [ShipperHQ](https://shipperhq.com).

---

## Installation
Install using Composer and run the Magento setup commands:

```bash
composer require webshopapps/module-productrate

# Enable the module (if not auto-enabled)
php bin/magento module:enable WebShopApps_ProductRate

# Apply database schema/data updates
php bin/magento setup:upgrade

# (Recommended) Clear caches and reindex
php bin/magento cache:flush
php bin/magento indexer:reindex

# (Production mode) Compile DI
php bin/magento setup:di:compile
```

---

## Requirements

- Magento 2.4.4+
    - Compatibility with earlier editions is possible but not maintained
    - Supports both Magento Opensource (Community) and Magento Commerce (Enterprise)

---

## Configuration

Follow these steps to get ProductRate running quickly:

1. **Enable ProductRate**
    - Log in to your Magento Admin.
    - Go to `Stores > Configuration > Sales > Shipping Methods > WebShopApps Product Rate`.
    - Set `Enabled` to `Yes` and save.

2. **Set a Shipping Fee on Products**
    - Go to `Catalog > Products` and edit a product.
    - Open the `Shipping` section.
    - Enter the per‑item amount in `Shipping Fee` (ProductRate) and save.

3. **Optional: Quantity Surcharges**
    - ProductRate can add a flat or percentage surcharge when more than one of the same product is in the cart.
    - See the [ProductRate configuration guide](https://docs.shipperhq.com/category/webshopapps-extensions/product-rate/) for setup details and examples.

4. **Test Checkout**
    - Add your product to the cart.
    - Go to the cart or checkout to estimate shipping. The ProductRate method will reflect the product’s fee plus any configured surcharge.

---

## Support

As a free extension, ShipperHQ ProductRate is provided as‑is without support.

ShipperHQ ProductRate is provided AS IS and we are not accepting feature requests at this time. Extended functionality and full support is available via [ShipperHQ](https://shipperhq.com).

---

## Frequently Asked Questions

### 1. How are fees calculated when customers buy multiple quantities?
The per‑product `Shipping Fee` is applied, and if configured, a flat or percentage surcharge is added when multiple units of the same product are in the cart. If several products have ProductRate fees, those fees are summed.

### 2. Can I set free shipping for a product?
Yes. Set the product’s `Shipping Fee` to `0.00`. You can still offer other carrier methods alongside ProductRate if desired.

### 3. Why doesn’t the ProductRate method appear at checkout?
- Ensure the method is enabled and the product has a `Shipping Fee` set.
- Confirm the product is shippable (not virtual) and you are viewing the correct website/store scope.
- Check Magento logs: `var/log/system.log` and `var/log/exception.log`.

---

## Credits
This extension borrows standard Magento shipping patterns to remain familiar and reliable for merchants.

The composer structure is taken from various sources, most heavily using structure from https://github.com/sjparkinson/static-review.

Assistance around composer, Magento2 structure, etc was also taken from these sources:

* https://github.com/Genmato/MultiStoreSearchFields
* https://alankent.wordpress.com/2014/08/03/creating-a-magento-2-composer-module/
* https://github.com/SchumacherFM/mage2-advanced-rest-permissions

---

## Contribution

Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

---

## License

See license files.

We also dutifully respect the Magento OSL license.

---

## Copyright

Copyright (c) 2015 Zowta LLC & Zowta Ltd.
