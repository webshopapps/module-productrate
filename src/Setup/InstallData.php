<?php
/**
 * WebShopApps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * WebShopApps ProductRate
 *
 * @category WebShopApps
 * @package WebShopApps_ProductRate
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.WebShopApps.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author WebShopApps Team sales@webshopapps.com
 *
 */
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WebShopApps\ProductRate\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        /* ------ shipperhq_shipping_fee -------- */
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'shipperhq_shipping_fee', [
            'type'                     => 'decimal',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'input'                    => 'price',
            'label'                    => 'Shipping Fee',
            'global' =>\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                  => true,
            'required'                 => false,
            'visible_on_front'         => false,
            'is_html_allowed_on_front' => false,
            'searchable'               => false,
            'filterable'               => false,
            'comparable'               => false,
            'is_configurable'          => false,
            'unique'                   => false,
            'user_defined'             => true,
            'used_in_product_listing'  => false
        ]);

        /* ------ shipperhq_shipping_add_on_price -------- */
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'shipperhq_addon_price', [
            'type'                     => 'decimal',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'input'                    => 'price',
            'label'                    => 'Increment for Subsequent Items',
            'global' =>\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                  => true,
            'required'                 => false,
            'visible_on_front'         => false,
            'is_html_allowed_on_front' => false,
            'searchable'               => false,
            'filterable'               => false,
            'comparable'               => false,
            'is_configurable'          => false,
            'unique'                   => false,
            'user_defined'             => true,
            'used_in_product_listing'  => false,
            'note'                     => 'Price or Percentage'
        ]);

        /* ------ shipperhq_shipping_add_on_price -------- */
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'shipperhq_inc_percent', [
            'type'                     => 'decimal',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'input'                    => 'price',
            'label'                    => 'Calculate Additional using Percentages',
            'global' =>\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                  => true,
            'required'                 => false,
            'visible_on_front'         => false,
            'is_html_allowed_on_front' => false,
            'searchable'               => false,
            'filterable'               => false,
            'comparable'               => false,
            'is_configurable'          => false,
            'unique'                   => false,
            'user_defined'             => true,
            'used_in_product_listing'  => false,
            'note'                     => 'Default is Price'
        ]);

        $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $attributeSetArr = $catalogSetup->getAllAttributeSetIds($entityTypeId);

        $stdAttributeCodes = ['shipperhq_shipping_fee' => '1',  'shipperhq_shipping_addon' => '2'];

        foreach ($attributeSetArr as $attributeSetId) {
            $catalogSetup->addAttributeGroup($entityTypeId, $attributeSetId, 'Shipping', '99');

            $attributeGroupId = $catalogSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Shipping');

            foreach ($stdAttributeCodes as $code => $sort) {
                $attributeId = $catalogSetup->getAttributeId($entityTypeId, $code);
                $catalogSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $attributeGroupId,
                    $attributeId,
                    $sort
                );
            }
        }

        $installer->endSetup();
    }
}
