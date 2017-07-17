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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        //SHQ16-2157 if less than 1.0.1 then update attributes
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updateProductAttributes($catalogSetup);
        }

        $installer->endSetup();
    }

    /**
     * Updates shipperhq_inc_percent attribute
     *
     * @param ModuleDataSetupInterface $catalogSetup
     * @return void
     */
    private function updateProductAttributes($catalogSetup)
    {
        /* ------ shipperhq_inc_percent -------- */
        $catalogSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipperhq_inc_percent',
            [
                'backend_model' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'backend_type' => 'varchar',
                'frontend_input' => 'select',
                'source_model' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'default_value' => '0'
            ]
        );

        $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $attributeSetArr = $catalogSetup->getAllAttributeSetIds($entityTypeId);

        $stdAttributeCodes = ['shipperhq_shipping_fee' => '1',  'shipperhq_addon_price' => '2'];

        foreach ($attributeSetArr as $attributeSetId) {
            //SHQ16-2123 handle migrated instances from M1 to M2
            $migrated = $catalogSetup->getAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                'migration-shipping'
            );
            if($migrated !== false) {
                $catalogSetup->removeAttributeGroup($entityTypeId, $attributeSetId, 'migration-shipping');
            }

            $attributeGroupId = $catalogSetup->getAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                'Shipping'
            );

            if (!$attributeGroupId) {
                $catalogSetup->addAttributeGroup($entityTypeId, $attributeSetId, 'Shipping', '99');
            }

            $attributeGroupId = $catalogSetup->getAttributeGroupId(
                $entityTypeId,
                $attributeSetId,
                'Shipping'
            );

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
    }
}