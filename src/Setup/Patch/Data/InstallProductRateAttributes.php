<?php
/*
 * ShipperHQ
 *
 * @category ShipperHQ
 * @package WebShopApps\ProductRate
 * @copyright Copyright (c) 2022 Zowta LTD and Zowta LLC (http://www.ShipperHQ.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author ShipperHQ Team sales@shipperhq.com
 */
declare(strict_types=1);

namespace WebShopApps\ProductRate\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallProductRateAttributes implements DataPatchInterface
{
    /**
     * Category setup factory
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @param ModuleDataSetupInterface   $moduleDataSetup
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param CategorySetupFactory       $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface   $moduleDataSetup,
        AttributeCollectionFactory $attributeCollectionFactory,
        CategorySetupFactory       $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory
            ?: ObjectManager::getInstance()->get(AttributeCollectionFactory::class);
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Do Upgrade
     * @return void
     */
    public function apply()
    {
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

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

        /* ------ shipperhq_inc_percent -------- */
        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipperhq_inc_percent',
            [
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'label' => 'Calculate Additional using Percentages',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
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
            ]
        );

        $entityTypeId = $catalogSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetArr = $catalogSetup->getAllAttributeSetIds($entityTypeId);
        $stdAttributeCodes = ['shipperhq_shipping_fee' => '1',  'shipperhq_addon_price' => '2'];

        foreach ($attributeSetArr as $attributeSetId) {
            //SHQ16-2123 handle migrated instances from M1 to M2
            $migrateGroupId = $catalogSetup->getAttributeGroup($entityTypeId, $attributeSetId, 'migration-shipping');
            $existingAttributeIds = [];
            if ($migrateGroupId) {
                $existingAttributeIds = $this->getNonShqAttributeIds(
                    $catalogSetup,
                    'migration-shipping',
                    $attributeSetId
                );
                $catalogSetup->removeAttributeGroup($entityTypeId, $attributeSetId, 'migration-shipping');
            }

            // SHQ18-2929 In M2.3.3 this group already exists. Don't create a duplicate
            if (!$catalogSetup->getAttributeGroup($entityTypeId, $attributeSetId, 'Shipping')) {
                $catalogSetup->addAttributeGroup($entityTypeId, $attributeSetId, 'Shipping', '99');
            }

            $attributeGroupId = $catalogSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Shipping');
            $ourAttributeIds = [];

            foreach ($stdAttributeCodes as $code => $sort) {
                $attributeId = $catalogSetup->getAttributeId($entityTypeId, $code);
                $ourAttributeIds[] = $attributeId;
                $catalogSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $attributeGroupId,
                    $attributeId,
                    $sort
                );
            }
            // SHQ18-2825 Add any attributes that were in migration-shipping that were not our attributes back
            if (count($existingAttributeIds)) {
                $attributeIdsToAdd = array_diff($existingAttributeIds, $ourAttributeIds);
                foreach ($attributeIdsToAdd as $attributeId) {
                    $catalogSetup->addAttributeToGroup(
                        $entityTypeId,
                        $attributeSetId,
                        $attributeGroupId,
                        $attributeId,
                        10
                    );
                }
            }
        }
    }

    /**
     * SHQ18-2825 Gets all attribute IDs for a given attribute group
     *
     * @param $catalogSetup
     * @param $attributeGroupName
     * @param $attributeSetId
     *
     * @return array
     */
    private function getNonShqAttributeIds($catalogSetup, $attributeGroupName, $attributeSetId)
    {
        $entityTypeId = $catalogSetup->getEntityTypeId(Product::ENTITY);
        $attributeGroupId = $catalogSetup->getAttributeGroupId(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupName
        );
        $collection = $this->attributeCollectionFactory->create();
        $collection->setAttributeGroupFilter($attributeGroupId);
        $allAttributeIds = [];
        foreach ($collection->getItems() as $attribute) {
            $allAttributeIds[] = $attribute->getAttributeId();
        }

        return $allAttributeIds;
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
