<?php
namespace Mageplaza\ProductType\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $fieldList = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'cost',
            'tier_price',
            'weight',
        ];
        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array('printable_product_type', $applyTo)) {
                $applyTo[] = 'printable_product_type';
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }


        $applyTo = explode(',', $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cost', 'apply_to'));
        unset($applyTo[array_search('printable_product_type', $applyTo)]);
        $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cost', 'apply_to', implode(',', $applyTo));


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'price_my_type',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Printable',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'printable_product_type'
            ]
        );

    }
}
