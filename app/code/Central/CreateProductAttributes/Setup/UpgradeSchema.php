<?php

namespace Central\CreateProductAttributes\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Central\CreateProductAttributes\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const ATTR_ONE_YEAR_WARRANTY = 'one_year_warranty';
    const ATTR_FREE_GIFT = 'free_gift';
    const ATTR_FREE_INSTALLATION = 'free_installation';
    const ATTR_FREE_DELIVERY = 'free_delivery';
    const ATTR_EASY_PAYMENT = 'easy_payment';
    const ATTR_CLICK_COLLECT = 'click_collect';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected $eavSetupFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    protected $eavSetup;
    /**
     * @var \Central\CreateProductAttributes\Model\AttributeSet
     */
    protected $attributeSet;
    /**
     * @var \Central\CreateProductAttributes\Helper\Data
     */
    protected $helper;

    /**
     * UpgradeSchema constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $eavSetup
     * @param \Central\CreateProductAttributes\Helper\Data $helper
     * @param \Central\CreateProductAttributes\Model\AttributeSet $attributeSet
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $eavSetup,
        \Central\CreateProductAttributes\Helper\Data $helper,
        \Central\CreateProductAttributes\Model\AttributeSet $attributeSet
    ) {

        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavSetup = $eavSetup;
        $this->attributeSet = $attributeSet;
        $this->helper = $helper;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $productEntityId = Product::ENTITY;

        /**
         * @var \Magento\Eav\Setup\EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->eavSetup]);
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $option['values'] = ['Full payment', 'Installment', 'Bank transfer'];
            $attributes = [
                'payment_method' => [
                    'group' => 'General',
                    'type' => 'varchar',
                    'input' => 'multiselect',
                    'label' => 'Payment method',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'option' => $option,
                    'attribute_set' => null

                ]
            ];
            foreach ($attributes as $attributeCode => $attr) {
                $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attr);
            }
            $file = '0.0.2/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $file = '0.0.3/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $file = '0.0.4/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $removeAttributes = [
                'ferquency_response',
                'brightness_cd_m',
                'lift_amp_cut_system',
                'capacity',
                'back_and_sides'
            ];
            foreach ($removeAttributes as $removeAttribute) {
                $eavSetup->removeAttribute(Product::ENTITY, $removeAttribute);
            }
            //remove attribute set
            $removeAttributeSetArr = ['Canister Vacuum Cleaners', 'Chargers/Wall Chargers'];
            $this->attributeSet->removeAttributeSet($removeAttributeSetArr);
            $file = '0.0.5/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $file = '0.0.6/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $arr = [
                'group' => 'General',
                'type' => 'decimal',
                'label' => 'Weight',
                'input' => 'weight',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Weight',
                'input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight',
                'sort_order' => 5,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'required' => false,
                'searchable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => false,
                'is_visible_on_front' => false,
                'used_in_product_listing' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

            ];
            $attributeCode = 'weight';
            $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
            $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $arr);
        }
        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $arrAttributeDelete = ['drill_sizes', 'weight'];
            foreach ($arrAttributeDelete as $attributeCodeDelete) {
                $eavSetup->removeAttribute(Product::ENTITY, $attributeCodeDelete);
            }
            $arr = [
                'group' => 'General',
                'type' => 'decimal',
                'label' => 'Weight',
                'input' => 'weight',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Weight',
                'input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight',
                'sort_order' => 20,
                'is_used_in_grid' => false,
                'filterable' => true,
                'required' => false,
                'searchable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

            ];
            $removeAttributeSet = ['For TV antenna (Digital box)Headphones'];
            $this->attributeSet->removeAttributeSet($removeAttributeSet);

            $attributeCode = 'weight';
            $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $arr);

            $file = '0.0.8/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $file = '0.0.9/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            $attributes = [
                'brand' => [
                    'group' => 'General',
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Brand',
                    'backend' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => '',

                ],
                'mms_id' => [
                    'group' => 'General',
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'MMSID',
                    'backend' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => '',

                ]
            ];
            foreach ($attributes as $attributeCode => $attr) {
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attr);
            }
        }
        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $attributes = [
                'store_price' => [
                    'group' => 'General',
                    'type' => 'int',
                    'input' => 'price',
                    'label' => 'In-store price',
                    'backend' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => '',

                ]
            ];
            foreach ($attributes as $attributeCode => $attr) {
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attr);
            }
        }
        if (version_compare($context->getVersion(), '0.1.2', '<')) {
            $option['values'] = ["N/A"];
            $attributes = [
                'brand' => [
                    'group' => 'General',
                    'type' => 'varchar',
                    'input' => 'select',
                    'label' => 'Brand',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'option' => $option
                ]
            ];
            foreach ($attributes as $attributeCode => $attr) {
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attr);
            }
        }
        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $arrRemove = [
                '3in1_card_reader',
                '3flat_plug',
                '1round_2flat_plug',
                '1biground_2small',
                '2round_plug',
                '2flat_plug',
                '2p_round_plug',
                '3round_plug',
                '3d',
                '1_year_warranty',
            ];
            foreach ($arrRemove as $attributeCode) {
                $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
            }
            $eavSetup->addAttribute(
                Product::ENTITY,
                'one_year_warranty',
                [
                    'group' => 'general',
                    'type' => 'int',
                    'input' => 'boolean',
                    'label' => '1 Year Warranty',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'unique' => false
                ]
            );
            $file = '0.1.3/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.1.4', '<')) {
            $arr = [
                'free_gift' =>
                    [
                        'group' => 'general',
                        'type' => 'int',
                        'input' => 'boolean',
                        'label' => 'Free Gift',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false
                    ],
                'free_installation' =>
                    [
                        'group' => 'general',
                        'type' => 'int',
                        'input' => 'boolean',
                        'label' => 'Free installation',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false
                    ],
                'free_delivery' =>
                    [
                        'group' => 'general',
                        'type' => 'int',
                        'input' => 'boolean',
                        'label' => 'Free Delivery',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false
                    ],
                'easy_payment' =>
                    [
                        'group' => 'general',
                        'type' => 'int',
                        'input' => 'boolean',
                        'label' => 'Easy Payment',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false
                    ],
                'click_collect' =>
                    [
                        'group' => 'general',
                        'type' => 'int',
                        'input' => 'boolean',
                        'label' => 'Click & Collect',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false
                    ],
            ];
            foreach ($arr as $attributeCode => $data) {
                $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $data);
            }
        }
        if (version_compare($context->getVersion(), '0.1.5', '<')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'external_hard_disk_2.5');
            $eavSetup->removeAttribute(Product::ENTITY, 'nfc/ios');
            $eavSetup->removeAttribute(Product::ENTITY, 'version/compliant');
            $file = '0.1.5/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }
        if (version_compare($context->getVersion(), '0.1.6', '<')) {
            $attributeId = $eavSetup->getAttributeId(Product::ENTITY, 'custom_label_id');
            if ($attributeId) {
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $attributeId,
                    'is_visible',
                    1
                );
            }
        }
        if (version_compare($context->getVersion(), '0.1.7', '<')) {
            $attributeId = $eavSetup->getAttributeId(Product::ENTITY, 'custom_label_id');
            if ($attributeId) {
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $attributeId,
                    'is_required_in_admin_store',
                    0
                );
            }
        }

        if (version_compare($context->getVersion(), '0.1.8', '<')) {
            $attributes = [
                'brand' => [
                    'group' => 'General',
                    'type' => 'varchar',
                    'input' => 'select',
                    'label' => 'Brand',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            ];
            foreach ($attributes as $attributeCode => $attr) {
                $eavSetup
                    ->addAttribute(Product::ENTITY, $attributeCode, $attr);
            }
        }

        // Remove some attribute
        if (version_compare($context->getVersion(), '0.1.9', '<')) {
            $attributeId = $eavSetup->getAttributeId($productEntityId, 'custom_label_id');
            if ($attributeId) {
                $eavSetup->updateAttribute($productEntityId, 'custom_label_id', 'is_required', false);
            }
            $eavSetup->removeAttribute($productEntityId, 'power_handling_watts_rms');
            $eavSetup->removeAttribute($productEntityId, 'timer_off_hours');
            $eavSetup->removeAttribute($productEntityId, 'cord_length');
            $eavSetup->removeAttribute($productEntityId, 'adjusting_the_wind_level2');
            $eavSetup->removeAttribute($productEntityId, 'weight_g');
            $eavSetup->removeAttribute($productEntityId, 'weight_approx');
        }

        // Remove some attribute
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            $removeAttributeSet = ['Dryer – Venting', 'Hand Free Kits '];
            $this->attributeSet->removeAttributeSet($removeAttributeSet);
            $eavSetup->removeAttribute($productEntityId, 'power_handling_watts');
            $file = '0.2.0/productAttributes.csv';
            $productAttributesCsv = $this->helper->getCsvContent($file, false);
            $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv, true);
        }

        if (version_compare($context->getVersion(), '0.2.1', '<')) {
            $this->updateYesNoAttributeToNotRequired($eavSetup);
        }

        $installer->endSetup();
    }

    /**
     * Update attributes
     * -
     * one_year_warranty
     * free_gift
     * free_installation
     * free_delivery
     * easy_payment
     * click_collect
     * -
     * to not required
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @return $this
     */
    private function updateYesNoAttributeToNotRequired($eavSetup)
    {
        $attributesToUpdate = [
            self::ATTR_ONE_YEAR_WARRANTY,
            self::ATTR_FREE_GIFT,
            self::ATTR_FREE_INSTALLATION,
            self::ATTR_FREE_DELIVERY,
            self::ATTR_EASY_PAYMENT,
            self::ATTR_CLICK_COLLECT
        ];

        foreach ($attributesToUpdate as $attribute) {
            $eavSetup->updateAttribute(
                Product::ENTITY,
                $attribute,
                'is_required',
                false
            );
        }
    }
}
